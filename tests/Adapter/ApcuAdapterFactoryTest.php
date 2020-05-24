<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Apcu\ApcuCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\ApcuAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;

class ApcuAdapterFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $mockContainer;

    protected function setup(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new ApcuAdapterFactory();

        $this->assertInstanceOf(ApcuAdapterFactory::class, $this->factory);
    }

    public function testInvoke(): void
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(ApcuCachePool::class, $instance);
    }
}
