<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Predis\PredisCachePool;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\PredisAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\PredisAdapterFactory
 */
class PredisAdapterFactoryTest extends TestCase
{
    /** @var PredisAdapterFactory */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected function setup(): void
    {
        if (!class_exists(Client::class)) {
            $this->markTestSkipped('Predis not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new PredisAdapterFactory();

        $this->assertInstanceOf(PredisAdapterFactory::class, $this->factory);
    }

    public function testInvokeWithService(): void
    {
        $cacheService = new Client();

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

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    public function testInvokeWithConnectionSettings(): void
    {
        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'servers' => [
                    'tcp:/127.0.0.1:6379'
                ],
            ]
        );

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    public function testInvokeWithMultipleConnectionSettings(): void
    {
        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'servers' => [
                    'tcp:/127.0.0.1:6379',
                    'tcp:/127.0.0.1:6379',
                ],
            ]
        );

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    public function testInvokeMissingServerAndService(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke($this->mockContainer, []);
    }
}
