<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\PHPArray\ArrayCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\ArrayAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;

class ArrayAdapterFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $mockContainer;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new ArrayAdapterFactory();

        $this->assertInstanceOf(ArrayAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(ArrayCachePool::class, $instance);
    }
}
