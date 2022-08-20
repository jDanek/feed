<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Adapter\ClientInterface;
use Psr\Log\LoggerInterface;

class Explorer
{

    /**
     * @var ClientInterface;
     */
    protected $client;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    const VALID_TYPES = [
        'application/atom+xml',
        'application/rss+xml'
    ];

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
     * Discover feeds from the webpage's headers
     * @param string $url
     * @return array
     */
    public function discover(string $url): array
    {
        $this->logger->info("discover feeds from {$url}");
        $stream = $this->client->getResponse($url, new \DateTime('@0'));

        $internalErrors = libxml_use_internal_errors(true);
        if (LIBXML_VERSION < 20900) {
            $entityLoaderDisabled = libxml_disable_entity_loader(true);
        }

        $feeds = $this->extractFeeds($stream->getBody());

        libxml_use_internal_errors($internalErrors);
        if (LIBXML_VERSION < 20900) {
            libxml_disable_entity_loader($entityLoaderDisabled);
        }

        return $feeds;
    }

    /**
     * Extract feeds Urls from HTML stream
     * @param string $html
     * @return array
     */
    protected function extractFeeds(string $html): array
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($html);

        $links = $dom->getElementsByTagName('link');
        $feeds = [];
        foreach ($links as $link) {
            if ($this->isFeedLink($link)) {
                $feeds[] = $link->getAttribute('href');
            }
        }

        return $feeds;
    }

    /**
     * Tells if the given Element contains a valid Feed Url
     * @param \DOMElement $element
     * @return bool
     */
    protected function isFeedLink(\DOMElement $element): bool
    {
        return $element->hasAttribute('type')
            && in_array($element->getAttribute('type'), self::VALID_TYPES);
    }
}
