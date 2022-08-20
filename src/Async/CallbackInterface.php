<?php declare(strict_types=1);

namespace Danek\FeedIo\Async;

use Danek\FeedIo\Reader\Result;

interface CallbackInterface
{

    /**
     * @param Result $result
     */
    public function process(Result $result): void;

    /**
     * @param Request $request
     * @param \Exception $exception
     */
    public function handleError(Request $request, \Exception $exception): void;
}
