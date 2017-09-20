<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

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

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new ApcuAdapterFactory();

        $this->assertInstanceOf(ApcuAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(ApcuCachePool::class, $instance);
    }
}
