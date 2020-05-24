<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Illuminate\IlluminateCachePool;
use Illuminate\Contracts\Cache\Store;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

class IlluminateAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): IlluminateCachePool
    {
        $cacheStoreName = (string)($options['store'] ?? '');

        $cacheStore = $container->get($cacheStoreName);

        if (!$cacheStore instanceof Store) {
            throw new InvalidConfigException(
                'Service provided for the Illuminate Cache must be an instance of ' . Store::class
            );
        }

        return new IlluminateCachePool($cacheStore);
    }
}
