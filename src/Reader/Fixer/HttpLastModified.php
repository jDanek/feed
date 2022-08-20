<?php declare(strict_types=1);

namespace Danek\FeedIo\Reader\Fixer;

use Danek\FeedIo\FeedInterface;
use Danek\FeedIo\Reader\FixerAbstract;
use Danek\FeedIo\Reader\Result;

class HttpLastModified extends FixerAbstract
{

    /**
     * @param Result $result
     * @return FixerAbstract
     */
    public function correct(Result $result): FixerAbstract
    {
        $feed = $result->getFeed();
        $response = $result->getResponse();

        if ($this->isInvalid($feed) && $response->getLastModified() instanceof \DateTime) {
            $this->logger->debug("found last modified: " . $response->getLastModified()->format(DateTime::RSS));
            $feed->setLastModified($response->getLastModified());
            $this->correctItems($feed);
        }

        return $this;
    }

    protected function correctItems(FeedInterface $feed): void
    {
        foreach ($feed as $item) {
            $item->setLastModified($feed->getLastModified());
        }
    }

    /**
     * @param FeedInterface $feed
     * @return bool
     */
    protected function isInvalid(FeedInterface $feed): bool
    {
        return is_null($feed->getLastModified()) || $feed->getLastModified() == new \DateTime('@0');
    }
}
