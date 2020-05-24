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
use WShafer\PSR11PhpCache\Config\MainConfig;
use WShafer\PSR11PhpCache\Exception\InvalidContainerException;
use WShafer\PSR11PhpCache\Exception\MissingCacheConfigException;

class PhpCacheFactory
{
    protected $configKey = 'default';

    public function __invoke(ContainerInterface $container): CacheItemPoolInterface
    {
        $config = $this->getConfig($container);

        $serviceName = $this->getConfigKey();

        $serviceConfig = $config->getCacheConfig($serviceName);

        $pool = $this->getCachePool($container, $serviceConfig);

        if (
            $pool instanceof LoggerAwareInterface
            && $serviceConfig->getLogger()
        ) {
            /** @var LoggerInterface $logger */
            $logger = $container->get($serviceConfig->getLogger());
            $pool->setLogger($logger);
        }

        if (
            $pool instanceof HierarchicalPoolInterface
            && !empty($serviceConfig->getNamespace())
        ) {
            return new NamespacedCachePool($pool, $serviceConfig->getNamespace());
        }

        if (!empty($serviceConfig->getPrefix())) {
            return new PrefixedCachePool($pool, $serviceConfig->getPrefix());
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
        $factory = new static();
        $factory->setConfigKey($name);
        return $factory($arguments[0]);
    }

    public function getConfigKey(): string
    {
        return $this->configKey;
    }

    public function setConfigKey(string $key): void
    {
        $this->configKey = $key;
    }

    protected function getConfig(ContainerInterface $container): MainConfig
    {
        $configArray = $this->getConfigArray($container);

        return new MainConfig($configArray);
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

        // Zend uses config key
        if ($container->has('config')) {
            return $container->get('config')['caches'] ?? [];
        }

        throw new MissingCacheConfigException(
            'Unable to locate the config inside the container'
        );
    }

    protected function getCachePool(ContainerInterface $container, MainConfig $serviceConfig): CacheItemPoolInterface
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
            return new $type;
        }

        $mapper = new AdapterMapper();
        return $mapper->map($type);
    }
}
