<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Doctrine\DoctrineCachePool;
use Doctrine\Common\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\DoctrineCacheAdapterFactory;

class DoctrineCacheAdapterFactoryTest extends TestCase
{
    /** @var DoctrineCacheAdapterFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        if (!interface_exists(Cache::class)) {
            $this->markTestSkipped('Doctrine Cache not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new DoctrineCacheAdapterFactory();

        $this->assertInstanceOf(DoctrineCacheAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $cacheService = $this->createMock(Cache::class);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'service' => 'my-service'
        ]);

        $this->assertInstanceOf(DoctrineCachePool::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingServersAndService()
    {
        $this->factory->__invoke($this->mockContainer, []);
    }
}
