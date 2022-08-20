<?php declare(strict_types=1);

namespace Danek\FeedIo\Feed;

interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array;
}
