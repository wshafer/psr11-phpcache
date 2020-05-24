<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Apc\ApcCachePool;
use Psr\Container\ContainerInterface;

class ApcAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): ApcCachePool
    {
        $skipOnCli = (boolean)($options['skipOnCli'] ?? false);

        return new ApcCachePool($skipOnCli);
    }
}
