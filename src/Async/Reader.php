<?php declare(strict_types=1);

namespace Danek\FeedIo\Async;

use Danek\FeedIo\Adapter\Guzzle\Client;
use Danek\FeedIo\Adapter\Guzzle\Async\ReaderInterface;
use GuzzleHttp\Promise\EachPromise;
use Danek\FeedIo\Reader as MainReader;
use Danek\FeedIo\Reader\Result;
use Danek\FeedIo\FeedInterface;

class Reader implements ReaderInterface
{
    /**
     * @var MainReader
     */
    protected $reader;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var CallbackInterface
     */
    protected $callback;

    /**
     * @var string
     */
    protected $feedClass;

    /**
     * Reader constructor.
     * @param MainReader $reader
     * @param Client $client
     * @param CallbackInterface $callback
     * @param string $feedClass
     */
    public function __construct(MainReader $reader, Client $client, CallbackInterface $callback, string $feedClass)
    {
        error_log("Async reading is deprecated and will be removed in v5.0.", E_USER_DEPRECATED);
        $this->reader = $reader;
        $this->client = $client;
        $this->callback = $callback;
        $this->feedClass = $feedClass;
    }

    /**
     * @param iterable $requests
     */
    public function process(iterable $requests): void
    {
        $promises = $this->client->getPromises($requests, $this);

        (new EachPromise($promises))->promise()->wait();
    }

    /**
     * @param Request $request
     * @throws \ReflectionException
     */
    public function handle(Request $request): void
    {
        $feed = $this->newFeed();
        $document = $this->reader->handleResponse($request->getResponse(), $feed);
        $result = new Result($document, $feed, $request->getModifiedSince(), $request->getResponse(), $request->getUrl());
        $this->callback->process($result);
    }

    /**
     * @param Request $request
     * @param \Exception $e
     */
    public function handleError(Request $request, \Exception $e): void
    {
        $this->callback->handleError($request, $e);
    }

    /**
     * @return FeedInterface
     * @throws \ReflectionException
     */
    public function newFeed(): FeedInterface
    {
        $reflection = new \ReflectionClass($this->feedClass);

        return $reflection->newInstanceArgs();
    }
}
