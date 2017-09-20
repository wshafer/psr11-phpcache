<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Void\VoidCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Adapter\VoidAdapterFactory;

class VoidAdapterFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $mockContainer;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new VoidAdapterFactory();

        $this->assertInstanceOf(VoidAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(VoidCachePool::class, $instance);
    }
}
