<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Config;

use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

class ConfigCollection
{
    protected $collection = [];

    public function __construct(array $collection = [])
    {
        $this->collection = $collection;
    }

    public function getCacheConfig(string $configName = 'default'): Config
    {
        $config = $this->collection[$configName] ?? [];
        if (empty($config)) {
            throw new InvalidConfigException('configName not found');
        }

        return new Config($config);
    }
}
