<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Chain\CachePoolChain;
use Doctrine\Common\Cache\Cache;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Psr\SimpleCache\CacheInterface;
use WShafer\PSR11PhpCache\Adapter\ChainCacheAdapterFactory;

class ChainCacheAdapterFactoryTest extends TestCase
{
    /** @var ChainCacheAdapterFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        if (!interface_exists(Cache::class)) {
            $this->markTestSkipped('Doctrine Cache not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new ChainCacheAdapterFactory();

        $this->assertInstanceOf(ChainCacheAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $cacheService = $this->createMock(CacheItemPoolInterface::class);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'services' => ['my-service']
        ]);

        $this->assertInstanceOf(CachePoolChain::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingServersAndService()
    {
        $this->factory->__invoke($this->mockContainer, []);
    }
}
