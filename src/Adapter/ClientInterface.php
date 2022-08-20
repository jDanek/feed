<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter;

/**
 * Describes a HTTP Client used by \Danek\FeedIo\Reader
 *
 * getResponse() MUST return an instance of \Danek\FeedIo\Adapter\ResponseInterface or throw an exception
 *
 */
interface ClientInterface
{

    /**
     * @param string $url
     * @param \DateTime $modifiedSince
     * @return ResponseInterface
     * @throws ServerErrorException
     * @throws NotFoundException
     */
    public function getResponse(string $url, \DateTime $modifiedSince): ResponseInterface;
}
