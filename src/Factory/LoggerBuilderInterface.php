<?php declare(strict_types=1);

namespace Danek\FeedIo\Factory;

use Psr\Log\LoggerInterface;

/**
 * @package FeedIo
 */
interface LoggerBuilderInterface extends BuilderInterface
{

    /**
     * This method MUST return a valid PSR3 logger
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface;
}
