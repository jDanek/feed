<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter;

/**
 * Fake HTTP client
 */
class NullClient implements ClientInterface
{
    /**
     * @param string $url
     * @param \DateTime $modifiedSince
     * @return ResponseInterface
     */
    public function getResponse(string $url, \DateTime $modifiedSince): ResponseInterface
    {
        return new NullResponse();
    }
}
