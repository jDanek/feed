<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Feed\ItemInterface;

interface FilterInterface
{
    /**
     * @param ItemInterface $item
     * @return bool
     */
    public function isValid(ItemInterface $item): bool;
}
