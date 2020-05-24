<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Apcu\ApcuCachePool;
use Psr\Container\ContainerInterface;

class ApcuAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): ApcuCachePool
    {
        $skipOnCli = (bool)($options['skipOnCli'] ?? false);

        return new ApcuCachePool($skipOnCli);
    }
}
