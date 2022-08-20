<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Parser\MissingFieldsException;
use Danek\FeedIo\Parser\UnsupportedFormatException;
use Danek\FeedIo\Reader\Document;
use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Psr\Log\LoggerInterface;

/**
 * Parses a document if its format matches the parser's standard
 *
 * Depends on :
 *  - Danek\FeedIo\StandardAbstract
 *  - Psr\Log\LoggerInterface
 *
 */
abstract class ParserAbstract
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var array[FilterInterface]
     */
    protected $filters = [];

    /**
     * @var StandardAbstract
     */
    protected $standard;

    /**
     * @param StandardAbstract $standard
     * @param LoggerInterface $logger
     */
    public function __construct(StandardAbstract $standard, LoggerInterface $logger)
    {
        $this->standard = $standard;
        $this->logger = $logger;
    }

    /**
     * Tries to parse the document
     *
     * @param Document $document
     * @param FeedInterface $feed
     * @return FeedInterface
     * @throws UnsupportedFormatException
     */
    public function parse(Document $document, FeedInterface $feed): FeedInterface
    {
        if (!$this->standard->canHandle($document)) {
            throw new UnsupportedFormatException('this is not a supported format');
        }

        $this->checkBodyStructure($document, $this->standard->getMandatoryFields());
        $this->parseContent($document, $feed);

        return $feed;
    }

    /**
     * This method is called by parse() if and only if the checkBodyStructure was successful
     *
     * @param Document $document
     * @param FeedInterface $feed
     * @return FeedInterface
     */
    abstract public function parseContent(Document $document, FeedInterface $feed): FeedInterface;

    /**
     * @param Document $document
     * @param iterable $mandatoryFields
     * @return bool
     * @throws MissingFieldsException
     */
    abstract public function checkBodyStructure(Document $document, iterable $mandatoryFields): bool;

    /**
     * @return StandardAbstract
     */
    public function getStandard(): StandardAbstract
    {
        return $this->standard;
    }

    /**
     * @param FeedInterface $feed
     * @param NodeInterface $item
     * @return ParserAbstract
     */
    public function addValidItem(FeedInterface $feed, NodeInterface $item): ParserAbstract
    {
        if ($item instanceof ItemInterface && $this->isValid($item)) {
            $feed->add($item);
        }

        return $this;
    }

    /**
     * @param ItemInterface $item
     * @return bool
     */
    public function isValid(ItemInterface $item): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->isValid($item)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FilterInterface $filter
     * @return ParserAbstract
     */
    public function addFilter(FilterInterface $filter): ParserAbstract
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * Reset filters
     * @return ParserAbstract
     */
    public function resetFilters(): ParserAbstract
    {
        $this->filters = [];

        return $this;
    }
}
