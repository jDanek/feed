<?php declare(strict_types=1);

namespace Danek\FeedIo;

interface FormatterInterface
{

    /**
     * @param FeedInterface $feed
     * @return string
     */
    public function toString(FeedInterface $feed): string;
}
