<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Adapter\ClientInterface;
use Danek\FeedIo\Factory\MissingDependencyException;
use Danek\FeedIo\Factory\LoggerBuilderInterface;
use Danek\FeedIo\Factory\ClientBuilderInterface;
use Danek\FeedIo\Factory\BuilderInterface;
use Psr\Log\LoggerInterface;

class Factory
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var ClientBuilderInterface
     */
    protected $clientBuilder;

    /**
     * @var LoggerBuilderInterface
     */
    protected $loggerBuilder;

    /**
     * @param array $loggerConfig
     * @param array $clientConfig
     * @return Factory
     * @throws \ReflectionException
     */
    public static function create(
        array $loggerConfig = [
            'builder' => 'NullLogger',
            'config' => [],
        ],
        array $clientConfig = [
            'builder' => 'GuzzleClient',
            'config' => [],
        ]
    ): Factory
    {
        $factory = new static();

        $factory->setClientBuilder(
            $factory->getBuilder($clientConfig['builder'], $factory->extractConfig($clientConfig))
        )
            ->setLoggerBuilder(
                $factory->getBuilder($loggerConfig['builder'], $factory->extractConfig($loggerConfig))
            );

        return $factory;
    }

    /**
     * @param ClientBuilderInterface $clientBuilder
     * @return Factory
     */
    public function setClientBuilder(ClientBuilderInterface $clientBuilder): Factory
    {
        $this->clientBuilder = $clientBuilder;

        return $this;
    }

    /**
     * @param string $builder
     * @param array $args
     * @return BuilderInterface
     * @throws \ReflectionException
     */
    public function getBuilder(string $builder, array $args = []): BuilderInterface
    {
        $class = "\\Danek\\FeedIo\\Factory\\Builder\\{$builder}Builder";

        if (!class_exists($class)) {
            $class = $builder;
        }

        $reflection = new \ReflectionClass($class);

        // Pass args only if constructor has
        return $reflection->newInstanceArgs([$args]);
    }

    /**
     * @param array $builderConfig
     *
     * @return array
     */
    public function extractConfig(array $builderConfig): array
    {
        return $builderConfig['config'] ?? [];
    }

    /**
     * @return FeedIo
     */
    public function getFeedIo(): FeedIo
    {
        return new FeedIo(
            $this->clientBuilder->getClient(),
            $this->loggerBuilder->getLogger()
        );
    }

    /**
     * @param LoggerBuilderInterface $loggerBuilder
     *
     * @return Factory
     */
    public function setLoggerBuilder(LoggerBuilderInterface $loggerBuilder): Factory
    {
        $this->loggerBuilder = $loggerBuilder;

        return $this;
    }

    /**
     * @param BuilderInterface $builder
     *
     * @return boolean true if the dependency is met
     */
    public function checkDependency(BuilderInterface $builder): bool
    {
        if (!class_exists($builder->getMainClassName())) {
            $message = "missing {$builder->getPackageName()}, please install it using composer : composer require {$builder->getPackageName()}";
            throw new MissingDependencyException($message);
        }

        return true;
    }
}
