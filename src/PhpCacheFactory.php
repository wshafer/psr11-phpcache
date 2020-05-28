<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache;

use Cache\Hierarchy\HierarchicalPoolInterface;
use Cache\Namespaced\NamespacedCachePool;
use Cache\Prefixed\PrefixedCachePool;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use WShafer\PSR11PhpCache\Adapter\AdapterMapper;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Config\Config;
use WShafer\PSR11PhpCache\Config\ConfigCollection;
use WShafer\PSR11PhpCache\Exception\InvalidContainerException;
use WShafer\PSR11PhpCache\Exception\MissingCacheConfigException;

class PhpCacheFactory
{
    protected $configKey = 'default';

    public function __construct(string $configKey = 'default')
    {
        $this->configKey = $configKey;
    }

    public function __invoke(ContainerInterface $container): CacheItemPoolInterface
    {
        $configCollection = $this->getConfigCollection($container);

        $config = $configCollection->getCacheConfig($this->configKey);

        $pool = $this->getCachePool($container, $config);

        if (
            $pool instanceof LoggerAwareInterface
            && $config->getLoggerServiceName()
        ) {
            /** @var LoggerInterface $logger */
            $logger = $container->get($config->getLoggerServiceName());
            $pool->setLogger($logger);
        }

        if (
            $pool instanceof HierarchicalPoolInterface
            && !empty($config->getNamespace())
        ) {
            return new NamespacedCachePool($pool, $config->getNamespace());
        }

        if (!empty($config->getPrefix())) {
            return new PrefixedCachePool($pool, $config->getPrefix());
        }

        return $pool;
    }

    /**
     * Magic method for constructing Cache pools by service name
     *
     * @param $name
     * @param $arguments
     *
     * @return CacheItemPoolInterface
     */
    public static function __callStatic($name, $arguments): CacheItemPoolInterface
    {
        if (
            empty($arguments[0])
            || !$arguments[0] instanceof ContainerInterface
        ) {
            throw new InvalidContainerException(
                'Argument 0 must be an instance of a PSR-11 container'
            );
        }
        $factory = new static($name);
        return $factory($arguments[0]);
    }

    protected function getConfigCollection(ContainerInterface $container): ConfigCollection
    {
        return new ConfigCollection(
            $this->getConfigArray($container)
        );
    }

    protected function getConfigArray(ContainerInterface $container): array
    {
        // Symfony config is parameters. //
        if (
            method_exists($container, 'getParameter')
            && method_exists($container, 'hasParameter')
            && $container->hasParameter('caches')
        ) {
            return $container->getParameter('caches') ?? [];
        }

        // Slim Config comes from "settings"
        if ($container->has('settings')) {
            return $container->get('settings')['caches'] ?? [];
        }

        // Laminas/Zend uses config key
        if ($container->has('config')) {
            return $container->get('config')['caches'] ?? [];
        }

        throw new MissingCacheConfigException(
            'Unable to locate the config inside the container'
        );
    }

    protected function getCachePool(ContainerInterface $container, Config $serviceConfig): CacheItemPoolInterface
    {
        $type = $serviceConfig->getType();
        $factory = $this->getFactory($type);

        return $factory($container, $serviceConfig->getOptions());
    }

    /**
     * @SuppressWarnings(PHPMD.MissingImport)
     *
     * @param $type
     * @return callable|FactoryInterface
     */
    protected function getFactory($type)
    {
        if (is_callable($type)) {
            return $type;
        }

        if (
            class_exists($type)
            && in_array(FactoryInterface::class, class_implements($type), true)
        ) {
            return new $type();
        }

        $mapper = new AdapterMapper();
        return $mapper->map($type);
    }
}
