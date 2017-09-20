<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Predis\PredisCachePool;
use Cache\Adapter\Redis\RedisCachePool;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\PredisAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\RedisAdapterFactory;

class RedisAdapterFactoryTest extends TestCase
{
    /** @var RedisAdapterFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new RedisAdapterFactory();

        $this->assertInstanceOf(RedisAdapterFactory::class, $this->factory);
    }

    public function testInvokeWithService()
    {
        $cacheService = new \Redis();

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'service' => 'my-service'
        ]);

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    public function testInvokeWithConnectionSettings()
    {
        $instance = $this->factory->__invoke($this->mockContainer, [
            'server'      => [
                'host'       => '127.0.0.1',
                'persistent' => false,
            ],
        ]);

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    public function testInvokeWithPersistentConnectionSettings()
    {
        $instance = $this->factory->__invoke($this->mockContainer, [
            'server'      => [
                'host'       => '127.0.0.1',
                'persistent' => true,
            ],
        ]);

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingServerAndService()
    {
        $this->factory->__invoke($this->mockContainer, []);
    }
}
