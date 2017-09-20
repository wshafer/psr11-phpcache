<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Config;

use WShafer\PSR11PhpCache\Exception\MissingCacheConfigException;
use Zend\Config\Config;

class MainConfig extends Config
{
    /**
     * @param string $name
     *
     * @return static
     * @throws MissingCacheConfigException
     */
    public function getCacheConfig(string $name)
    {
        $default = new self(['type' => 'void']);

        return $this->get($name, $default);
    }

    /**
     * @return string|callable
     */
    public function getType()
    {
        $type = $this->get('type', 'void');

        return $type;
    }

    public function getOptions() : array
    {
        return $this->get('options', new static([]))->toArray();
    }

    /**
     * @return string|null
     */
    public function getNamespace()
    {
        return $this->get('namespace');
    }

    /**
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->get('prefix');
    }

    public function getLogger()
    {
        return $this->get('logger');
    }
}
