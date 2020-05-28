<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Redis\RedisCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Redis;
use WShafer\PSR11PhpCache\Adapter\RedisAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\RedisAdapterFactory
 */
class RedisAdapterFactoryTest extends TestCase
{
    /** @var RedisAdapterFactory */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected function setup(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new RedisAdapterFactory();

        $this->assertInstanceOf(RedisAdapterFactory::class, $this->factory);
    }

    public function testInvokeWithService(): void
    {
        $cacheService = new Redis();

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'service' => 'my-service'
            ]
        );

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    public function testInvokeWithConnectionSettings(): void
    {
        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'server' => [
                    'host' => '127.0.0.1',
                    'persistent' => false,
                ],
            ]
        );

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    public function testInvokeWithPersistentConnectionSettings(): void
    {
        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'server' => [
                    'host' => '127.0.0.1',
                    'persistent' => true,
                ],
            ]
        );

        $this->assertInstanceOf(RedisCachePool::class, $instance);
    }

    public function testInvokeMissingServerAndService(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke($this->mockContainer, []);
    }
}
