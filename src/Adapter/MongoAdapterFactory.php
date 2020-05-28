<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\MongoDB\MongoDBCachePool;
use MongoDB\Collection;
use MongoDB\Driver\Manager;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;
use WShafer\PSR11PhpCache\Exception\MissingExtensionException;
use WShafer\PSR11PhpCache\Exception\MissingLibraryException;

class MongoAdapterFactory implements FactoryInterface
{
    /**
     * MongoAdapterFactory constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!extension_loaded('mongodb')) {
            throw new MissingExtensionException(
                'mongodb extension not installed.'
            );
        }

        if (!class_exists(Collection::class)) {
            throw new MissingLibraryException(
                'composer package mongodb/mongodb not installed.'
            );
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return MongoDBCachePool
     */
    public function __invoke(ContainerInterface $container, array $options): MongoDBCachePool
    {
        $instance = $this->getMongoInstance($container, $options);
        return new MongoDBCachePool($instance);
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     * @return \MongoDB\Collection
     */
    protected function getMongoInstance(ContainerInterface $container, array $options): Collection
    {
        if (
            empty($options['service'])
            && empty($options['dsn'])
        ) {
            throw new InvalidConfigException(
                'You must provide either a container service or a connection string to use'
            );
        }

        if (!empty($options['service'])) {
            return $this->getInstanceFromContainer($container, $options['service']);
        }

        return $this->getInstanceFromConfig($options);
    }

    protected function getInstanceFromContainer(ContainerInterface $container, $name)
    {
        return $container->get($name);
    }

    /**
     * @param array $options
     *
     * @return Collection
     *
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    protected function getInstanceFromConfig(array $options): Collection
    {
        $dsn = $options['dsn'] ?? null;
        $databaseName = $options['database'] ?? null;
        $collectionName = $options['collection'] ?? null;

        if (empty($databaseName)) {
            throw new InvalidConfigException(
                'You must provide a database name to use'
            );
        }

        if (empty($collectionName)) {
            throw new InvalidConfigException(
                'You must provide a collection name to use'
            );
        }

        $manager = new Manager($dsn);
        return MongoDBCachePool::createCollection($manager, $databaseName, $collectionName);
    }
}
