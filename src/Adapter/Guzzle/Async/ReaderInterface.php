<?php declare(strict_types=1);

namespace Danek\FeedIo\Adapter\Guzzle\Async;

use Danek\FeedIo\Async\Request;

interface ReaderInterface
{

    /**
     * @param Request $request
     */
    public function handle(Request $request): void;

    /**
     * @param Request $request
     * @param \Exception $e
     */
    public function handleError(Request $request, \Exception $e): void;
}
