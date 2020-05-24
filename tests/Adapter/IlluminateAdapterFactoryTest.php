<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Illuminate\IlluminateCachePool;
use Illuminate\Contracts\Cache\Store;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Adapter\IlluminateAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\IlluminateAdapterFactory
 */
class IlluminateAdapterFactoryTest extends TestCase
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var MockObject|Store */
    protected $mockStore;

    protected function setup(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockStore = $this->createMock(Store::class);

        $this->factory = new IlluminateAdapterFactory();

        $this->assertInstanceOf(IlluminateAdapterFactory::class, $this->factory);
    }

    public function testInvoke(): void
    {
        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($this->mockStore);

        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'store' => 'my-service'
            ]
        );

        $this->assertInstanceOf(IlluminateCachePool::class, $instance);
    }

    public function testInvokeNoFileSystem(): void
    {
        $this->expectException(InvalidConfigException::class);

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn(new stdClass());

        $this->factory->__invoke(
            $this->mockContainer,
            [
                'store' => 'my-service'
            ]
        );
    }
}
