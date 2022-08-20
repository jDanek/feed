<?php declare(strict_types=1);

namespace Danek\FeedIo\Async;

use Danek\FeedIo\Reader\Result;
use Psr\Log\LoggerInterface;

class DefaultCallback implements CallbackInterface
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function process(Result $result): void
    {
        $this->logger->info("feed processed : {$result->getUrl()} - title : {$result->getFeed()->getTitle()}");
    }

    /**
     * @inheritDoc
     */
    public function handleError(Request $request, \Exception $exception): void
    {
        $this->logger->warning("exception caught for {$request->getUrl()} : {$exception->getMessage()}");
    }
}
