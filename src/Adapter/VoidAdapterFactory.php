<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Void\VoidCachePool;
use Psr\Container\ContainerInterface;

class VoidAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options)
    {
        return new VoidCachePool();
    }
}
