<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\Cache;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;
use WShafer\PSR11PhpCache\Exception\MissingLibraryException;

class DoctrineCacheAdapterFactory implements FactoryInterface
{
    /**
     * MemcachedAdapterFactory constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!interface_exists(Cache::class)) {
            throw new MissingLibraryException(
                'Doctrine Cache is not installed.'
            );
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return DoctrineCachePool
     */
    public function __invoke(ContainerInterface $container, array $options): DoctrineCachePool
    {
        if (empty($options['service'])) {
            throw new InvalidConfigException(
                'You must provide a doctrine cache service name to use'
            );
        }

        $cache = $container->get($options['service']);
        return new DoctrineCachePool($cache);
    }
}
