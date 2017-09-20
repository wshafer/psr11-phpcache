<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Memcached\MemcachedCachePool;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;
use WShafer\PSR11PhpCache\Exception\MissingExtensionException;

class MemcachedAdapterFactory implements FactoryInterface
{
    /** @var \Memcached */
    protected $cache;

    /**
     * MemcachedAdapterFactory constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!extension_loaded('memcached')) {
            throw new MissingExtensionException(
                'Memcached extension not installed.'
            );
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return MemcachedCachePool
     */
    public function __invoke(ContainerInterface $container, array $options)
    {
        $this->cache = $this->getMemcachedInstance($container, $options);
        return new MemcachedCachePool($this->cache);
    }

    /**
     * Added for testing
     *
     * @return \Memcached
     */
    public function getCache()
    {
        return $this->cache;
    }

    protected function getMemcachedInstance(ContainerInterface $container, array $options)
    {
        if (empty($options['service'])
            && empty($options['servers'])
        ) {
            throw new InvalidConfigException(
                'You must provide either a container service or at least one server to use'
            );
        }

        if (!empty($options['service'])) {
            return $this->getInstanceFromContainer($container, $options['service']);
        }

        return $this->getInstanceFromConfig($options);
    }

    protected function getInstanceFromContainer(ContainerInterface $container, $name)
    {
        return $container->get($name);
    }

    protected function getInstanceFromConfig(array $options)
    {
        $persistentId     = $options['persistentId']     ?? null;
        $servers          = $options['servers']          ?? [];
        $memcachedOptions = $options['memcachedOptions'] ?? [];

        $instance = new \Memcached($persistentId);

        foreach ($servers as $server) {
            $this->addServer($instance, $server);
        }

        foreach ($memcachedOptions as $key => $value) {
            $instance->setOption($key, $value);
        }

        return $instance;
    }

    protected function addServer(\Memcached $instance, $server)
    {
        $serverList = $instance->getServerList();

        $host   = $server['host']   ?? null;
        $port   = $server['port']   ?? 11211;
        $weight = $server['weight'] ?? 0;

        if (empty($host)) {
            throw new InvalidConfigException(
                "Invalid host provided for Memcached server"
            );
        }

        foreach ($serverList as $addedServer) {
            if ($addedServer['host'] == $host
                && $addedServer['port'] == $port
            ) {
                // Server already added skipping
                return;
            }
        }

        $instance->addServer($host, $port, $weight);
    }
}
