<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Chain\CachePoolChain;
use Doctrine\Common\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\ChainCacheAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\ChainCacheAdapterFactory
 */
class ChainCacheAdapterFactoryTest extends TestCase
{
    /** @var ChainCacheAdapterFactory */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected function setup(): void
    {
        if (!interface_exists(Cache::class)) {
            $this->markTestSkipped('Doctrine Cache not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new ChainCacheAdapterFactory();

        $this->assertInstanceOf(ChainCacheAdapterFactory::class, $this->factory);
    }

    public function testInvoke(): void
    {
        $cacheService = $this->createMock(CacheItemPoolInterface::class);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'services' => ['my-service']
            ]
        );

        $this->assertInstanceOf(CachePoolChain::class, $instance);
    }

    public function testInvokeMissingServersAndService(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke($this->mockContainer, []);
    }
}
