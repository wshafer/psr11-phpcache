<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Config;

use WShafer\PSR11PhpCache\Exception\MissingCacheConfigException;

class Config
{
    /**
     * @var array
     */
    protected $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function getType()
    {
        $type = $this->config['type'] ?? '';
        if (empty($type)) {
            throw new MissingCacheConfigException('type is missing from cache config');
        }

        return $type;
    }

    public function getOptions(): array
    {
        return $this->config['options'] ?? [];
    }

    public function getNamespace(): ?string
    {
        return $this->config['namespace'] ?? null;
    }

    public function getPrefix(): ?string
    {
        return $this->config['prefix'] ?? null;
    }

    public function getLoggerServiceName(): ?string
    {
        return $this->config['logger'] ?? null;
    }
}
