<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter\Guzzle;

use Danek\FeedIo\Adapter\ClientInterface;
use Danek\FeedIo\Adapter\Guzzle\Async\ReaderInterface;
use Danek\FeedIo\Adapter\NotFoundException;
use Danek\FeedIo\Adapter\ResponseInterface;
use Danek\FeedIo\Adapter\ServerErrorException;
use Danek\FeedIo\Async\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Exception\BadResponseException;

/**
 * Guzzle dependent HTTP client
 */
class Client implements ClientInterface
{

    /**
     * Default user agent provided with the package
     */
    const DEFAULT_USER_AGENT = 'Mozilla/5.0 (X11; U; Linux i686; fr; rv:1.9.1.1) Gecko/20090715 Firefox/3.5.1';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $userAgent;

    /**
     * @param \GuzzleHttp\ClientInterface $guzzleClient
     * @param string $userAgent
     */
    public function __construct(\GuzzleHttp\ClientInterface $guzzleClient, string $userAgent = self::DEFAULT_USER_AGENT)
    {
        $this->guzzleClient = $guzzleClient;
        $this->userAgent = $userAgent;
    }

    /**
     * @param string $userAgent The new user-agent
     * @return Client
     */
    public function setUserAgent(string $userAgent): Client
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @param string $url
     * @param \DateTime $modifiedSince
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function getResponse(string $url, \DateTime $modifiedSince): ResponseInterface
    {
        $start = microtime(true);
        try {
            $options = $this->getOptions($modifiedSince);
            $psrResponse = $this->guzzleClient->request('get', $url, $options);
            $duration = $this->getDuration($start);
            return new Response($psrResponse, $duration);
        } catch (BadResponseException $e) {
            $duration = $this->getDuration($start);
            switch ((int)$e->getResponse()->getStatusCode()) {
                case 404:
                    $notFoundException = new NotFoundException($e->getMessage());
                    $notFoundException->setDuration($duration);
                    throw $notFoundException;
                default:
                    $serverErrorException = new ServerErrorException($e->getMessage());
                    $serverErrorException->setResponse($e->getResponse());
                    $serverErrorException->setDuration($duration);
                    throw $serverErrorException;
            }
        }
    }

    /**
     * @param float $start
     * @return int
     */
    protected function getDuration(float $start): int
    {
        return intval(round(microtime(true) - $start, 3) * 1000);
    }

    /**
     * @param iterable $requests
     * @param ReaderInterface $reader
     * @return \Generator
     */
    public function getPromises(iterable $requests, ReaderInterface $reader): \Generator
    {
        foreach ($requests as $request) {
            yield $this->getPromise($request, $reader);
        }
    }

    /**
     * @param Request $request
     * @param ReaderInterface $reader
     * @return PromiseInterface
     */
    protected function getPromise(Request $request, ReaderInterface $reader): PromiseInterface
    {
        $promise = $this->newPromise($request);

        $promise->then(function ($psrResponse) use ($request, $reader) {
            try {
                $request->setResponse(new Response($psrResponse, 0));
                $reader->handle($request);
            } catch (\Exception $e) {
                $reader->handleError($request, $e);
            }
        }, function ($error) use ($request, $reader) {
            $reader->handleError($request, $error);
        });

        return $promise;
    }

    /**
     * @param Request $request
     * @return PromiseInterface
     */
    protected function newPromise(Request $request): PromiseInterface
    {
        $options = $this->getOptions($request->getModifiedSince());

        return $this->guzzleClient->requestAsync('GET', $request->getUrl(), $options);
    }

    /**
     * @param \DateTime $modifiedSince
     * @return array
     */
    protected function getOptions(\DateTime $modifiedSince): array
    {
        return [
            'headers' => [
                'Accept-Encoding' => 'gzip, deflate',
                'User-Agent' => $this->userAgent,
                'If-Modified-Since' => $modifiedSince->format(\DateTime::RFC2822)
            ],
            'timeout' => 30,
        ];
    }
}
