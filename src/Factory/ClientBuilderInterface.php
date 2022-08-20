<?php declare(strict_types=1);

namespace Danek\FeedIo\Factory;

use Danek\FeedIo\Adapter\ClientInterface;

/**
 * @package Danek\FeedIo
 */
interface ClientBuilderInterface extends BuilderInterface
{

    /**
     * This method MUST return a \Danek\FeedIo\Adapter\ClientInterface instance
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;
}
