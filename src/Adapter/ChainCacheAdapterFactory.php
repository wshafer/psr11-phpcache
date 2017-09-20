<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Cache\Adapter\Chain\CachePoolChain;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

class ChainCacheAdapterFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param array              $options
     *
     * @return CachePoolChain
     */
    public function __invoke(ContainerInterface $container, array $options)
    {
        if (empty($options['services']) || !is_array($options['services'])) {
            throw new InvalidConfigException(
                'You must provide an array of preconfigured cache services to use for the chain'
            );
        }

        $skipOnFailure = $options['skipOnFailure'] ?? false;

        $pools = [];

        foreach ($options['services'] as $service) {
            $pools[] = $container->get($service);
        }

        return new CachePoolChain($pools, ['skip_on_failure' => $skipOnFailure]);
    }
}
