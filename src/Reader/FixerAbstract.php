<?php declare(strict_types=1);

namespace Danek\FeedIo\Reader;

use Psr\Log\LoggerInterface;

abstract class FixerAbstract
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger): FixerAbstract
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * @param Result $result
     * @return FixerAbstract
     */
    abstract public function correct(Result $result): FixerAbstract;
}
