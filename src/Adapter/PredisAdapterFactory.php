<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Predis\PredisCachePool;
use Predis\Client;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;
use WShafer\PSR11PhpCache\Exception\MissingLibraryException;

class PredisAdapterFactory implements FactoryInterface
{
    /**
     * PredisAdapterFactory constructor.
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        if (!class_exists(Client::class)) {
            throw new MissingLibraryException(
                'Predis library not installed'
            );
        }
    }

    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return PredisCachePool
     */
    public function __invoke(ContainerInterface $container, array $options): PredisCachePool
    {
        $instance = $this->getInstance($container, $options);
        return new PredisCachePool($instance);
    }

    protected function getInstance(ContainerInterface $container, array $options)
    {
        if (
            empty($options['service'])
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

    /**
     * @param ContainerInterface $container
     * @param                    $name
     *
     * @return Client
     */
    protected function getInstanceFromContainer(ContainerInterface $container, $name): Client
    {
        return $container->get($name);
    }

    protected function getInstanceFromConfig(array $options): Client
    {
        $servers = $options['servers'] ?? [];
        $connectionOptions = $options['connectionOptions'] ?? [];

        if (count($servers) === 1) {
            return new Client($servers[0], $connectionOptions);
        }

        return new Client($servers, $connectionOptions);
    }
}
