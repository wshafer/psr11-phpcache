<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Apc\ApcCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\ApcAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;

class ApcAdapterFactoryTest extends TestCase
{
    /**
     * @var FactoryInterface
     */
    protected $factory;

    protected $mockContainer;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->factory = new ApcAdapterFactory();

        $this->assertInstanceOf(ApcAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $instance = $this->factory->__invoke($this->mockContainer, []);
        $this->assertInstanceOf(ApcCachePool::class, $instance);
    }
}
