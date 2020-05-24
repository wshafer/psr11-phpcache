<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class AdapterMapper
{
    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string $type
     * @return \WShafer\PSR11PhpCache\Adapter\FactoryInterface|null
     */
    public function map(string $type): ?FactoryInterface
    {
        $type = strtolower($type);

        switch ($type) {
            case 'apc':
                return new ApcAdapterFactory();
            case 'apcu':
                return new ApcuAdapterFactory();
            case 'array':
                return new ArrayAdapterFactory();
            case 'chain':
                return new ChainCacheAdapterFactory();
            case 'doctrine':
                return new DoctrineCacheAdapterFactory();
            case 'filesystem':
                return new FileSystemAdapterFactory();
            case 'illuminate':
                return new IlluminateAdapterFactory();
            case 'memcached':
                return new MemcachedAdapterFactory();
            case 'mongodb':
            case 'mongo':
                return new MongoAdapterFactory();
            case 'predis':
                return new PredisAdapterFactory();
            case 'redis':
                return new RedisAdapterFactory();
            case 'void':
                return new VoidAdapterFactory();
        }

        return null;
    }
}
