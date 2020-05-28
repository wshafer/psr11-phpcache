<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Apc\ApcCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\ApcAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\ApcAdapterFactory
 */
class ApcAdapterFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $mockContainer;

    protected function setup(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new ApcAdapterFactory();

        $this->assertInstanceOf(ApcAdapterFactory::class, $this->factory);
    }

    public function testInvoke(): void
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(ApcCachePool::class, $instance);
    }
}
