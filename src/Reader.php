<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Adapter\ClientInterface;
use Danek\FeedIo\Adapter\ResponseInterface;
use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\Reader\ReadErrorException;
use Danek\FeedIo\Reader\Result;
use Danek\FeedIo\Reader\NoAccurateParserException;
use Psr\Log\LoggerInterface;

/**
 * Consumes feeds and return corresponding Result instances
 *
 * Depends on :
 *  - Danek\FeedIo\Adapter\ClientInterface
 *  - Psr\Log\LoggerInterface
 *
 * A Reader instance MUST have at least one parser added with the addParser() method to read feeds
 * It will throw a NoAccurateParserException if it cannot find a suitable parser for the feed.
 */
class Reader
{
    /**
     * @var ClientInterface;
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $parsers = [];

    /**
     * @param ClientInterface $client
     * @param LoggerInterface $logger
     */
    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    /**
     * @param ParserAbstract $parser
     * @return Reader
     */
    public function addParser(ParserAbstract $parser): Reader
    {
        $this->logger->debug("new parser added : " . get_class($parser->getStandard()));
        $this->parsers[] = $parser;

        return $this;
    }

    /**
     * adds a filter to every parsers
     *
     * @param FilterInterface $filter
     * @return Reader
     */
    public function addFilter(FilterInterface $filter): Reader
    {
        foreach ($this->parsers as $parser) {
            $parser->addFilter($filter);
        }

        return $this;
    }

    /**
     * Reset filters on every parsers
     * @return Reader
     */
    public function resetFilters(): Reader
    {
        foreach ($this->parsers as $parser) {
            $parser->resetFilters();
        }

        return $this;
    }

    /**
     * @param string $url
     * @param FeedInterface $feed
     * @param \DateTime|null $modifiedSince
     * @return Result
     */
    public function read(string $url, FeedInterface $feed, \DateTime $modifiedSince = null): Result
    {
        $this->logger->debug("start reading {$url}");
        if (is_null($modifiedSince)) {
            $this->logger->notice("no 'modifiedSince' parameter given, setting it to 01/01/1970");
            $modifiedSince = new \DateTime('1800-01-01');
        }

        try {
            $this->logger->info("hitting {$url}");
            $response = $this->client->getResponse($url, $modifiedSince);
            $document = $this->handleResponse($response, $feed);

            return new Result($document, $feed, $modifiedSince, $response, $url);
        } catch (\Exception $e) {
            $this->logger->warning("{$url} read error : {$e->getMessage()}");
            throw new ReadErrorException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param ResponseInterface $response
     * @param FeedInterface $feed
     * @return Document
     */
    public function handleResponse(ResponseInterface $response, FeedInterface $feed): Document
    {
        $this->logger->debug("response ok, now turning it into a document");
        $document = new Document($response->getBody());

        if ($response->isModified()) {
            $this->logger->info("the stream is modified, parsing it");
            $this->parseDocument($document, $feed);
        }

        return $document;
    }

    /**
     * @param Document $document
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws Parser\UnsupportedFormatException
     * @throws Reader\NoAccurateParserException
     */
    public function parseDocument(Document $document, FeedInterface $feed): FeedInterface
    {
        $parser = $this->getAccurateParser($document);
        $this->logger->debug("accurate parser : " . get_class($parser));

        return $parser->parse($document, $feed);
    }

    /**
     * @param Document $document
     * @return ParserAbstract
     * @throws Reader\NoAccurateParserException
     */
    public function getAccurateParser(Document $document): ParserAbstract
    {
        foreach ($this->parsers as $parser) {
            if ($parser->getStandard()->canHandle($document)) {
                return $parser;
            }
        }

        $message = 'No parser can handle this stream';
        $this->logger->error($message);
        throw new NoAccurateParserException($message);
    }
}
