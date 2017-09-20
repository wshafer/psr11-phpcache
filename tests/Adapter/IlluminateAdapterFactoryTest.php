<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Illuminate\IlluminateCachePool;
use Illuminate\Contracts\Cache\Store;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Adapter\IlluminateAdapterFactory;

class IlluminateAdapterFactoryTest extends TestCase
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Store */
    protected $mockStore;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockStore = $this->createMock(Store::class);

        $this->factory = new IlluminateAdapterFactory();

        $this->assertInstanceOf(IlluminateAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($this->mockStore);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'store' => 'my-service'
        ]);

        $this->assertInstanceOf(IlluminateCachePool::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeNoFileSystem()
    {
        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn(new \stdClass());

        $this->factory->__invoke($this->mockContainer, [
            'store' => 'my-service'
        ]);
    }

}
