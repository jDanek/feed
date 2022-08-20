<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter\FileSystem;

use Danek\FeedIo\Adapter\ClientInterface;
use Danek\FeedIo\Adapter\NotFoundException;
use Danek\FeedIo\Adapter\ResponseInterface;

/**
 * Filesystem client
 */
class Client implements ClientInterface
{

    /**
     * @param string $path
     * @param \DateTime $modifiedSince
     * @return ResponseInterface
     * @throws NotFoundException
     * @throws \Exception
     * @throws \Exception
     */
    public function getResponse(string $path, \DateTime $modifiedSince): ResponseInterface
    {
        if (file_exists($path)) {
            return new Response(
                file_get_contents($path),
                new \DateTime('@' . filemtime($path))
            );
        }

        throw new NotFoundException($path);
    }
}
