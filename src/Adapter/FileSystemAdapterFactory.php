<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\FilesystemInterface;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

class FileSystemAdapterFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options): FilesystemCachePool
    {
        $flySystemServiceName = (string)($options['flySystemService'] ?? '');
        $folder = (string)($options['folder'] ?? 'cache');

        $flySystem = $container->get($flySystemServiceName);

        if (!$flySystem instanceof FilesystemInterface) {
            throw new InvalidConfigException(
                'Service provided for the filesystem must be an instance of ' . FilesystemInterface::class
            );
        }

        return new FilesystemCachePool($flySystem, $folder);
    }
}
