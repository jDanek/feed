<?php declare(strict_types=1);

namespace Danek\FeedIo\Filter;

use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\FilterInterface;

class ModifiedSince implements FilterInterface
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * ModifiedSince constructor.
     * @param \DateTime $date
     */
    public function __construct(\DateTime $date)
    {
        $this->date = $date;
    }


    /**
     * @param ItemInterface $item
     * @return bool
     */
    public function isValid(ItemInterface $item): bool
    {
        if ($item->getLastModified() instanceof \DateTime) {
            return $item->getLastModified() > $this->date;
        }

        return true;
    }
}
