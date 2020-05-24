<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\PHPArray\ArrayCachePool;
use Psr\Container\ContainerInterface;

class ArrayAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): ArrayCachePool
    {
        return new ArrayCachePool();
    }
}
