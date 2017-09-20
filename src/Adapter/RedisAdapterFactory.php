<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Redis\RedisCachePool;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;
use WShafer\PSR11PhpCache\Exception\MissingExtensionException;

class RedisAdapterFactory implements FactoryInterface
{
    /**
     * PredisAdapterFactory constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!extension_loaded('redis')) {
            throw new MissingExtensionException(
                'Redis extension not installed'
            );
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return RedisCachePool
     */
    public function __invoke(ContainerInterface $container, array $options)
    {
        $instance = $this->getInstance($container, $options);
        return new RedisCachePool($instance);
    }

    public function getInstance(ContainerInterface $container, array $options)
    {
        if (empty($options['service'])
            && empty($options['server'])
        ) {
            throw new InvalidConfigException(
                'You must provide either a container service or a server connection'
            );
        }

        if (!empty($options['service'])) {
            return $this->getInstanceFromContainer($container, $options['service']);
        }

        return $this->getInstanceFromConfig($options);
    }

    /**
     * @param ContainerInterface $container
     * @param $name
     *
     * @return \Redis
     */
    public function getInstanceFromContainer(ContainerInterface $container, $name)
    {
        return $container->get($name);
    }

    public function getInstanceFromConfig(array $options)
    {
        $server       = $options['server']      ?? [];
        $host         = $server['host']         ?? null;
        $port         = $server['port']         ?? 6379;
        $timeout      = $server['timeout']      ?? 0.0;
        $persistent   = $server['persistent']   ?? true;
        $persistentId = $server['persistentId'] ?? 'phpcache';

        $connection = new \Redis();

        if (!$persistent) {
            $connection->connect($host, $port, $timeout);
            return $connection;
        }

        $connection->pconnect($host, $port, $timeout, $persistentId);
        return $connection;
    }
}
