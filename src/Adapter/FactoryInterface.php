<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Adapter;

use Psr\Container\ContainerInterface;

interface FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options);
}
