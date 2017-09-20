<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Predis\PredisCachePool;
use PHPUnit\Framework\TestCase;
use Predis\Client;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\PredisAdapterFactory;

class PredisAdapterFactoryTest extends TestCase
{
    /** @var PredisAdapterFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        if (!class_exists(Client::class)) {
            $this->markTestSkipped('Predis not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new PredisAdapterFactory();

        $this->assertInstanceOf(PredisAdapterFactory::class, $this->factory);
    }

    public function testInvokeWithService()
    {
        $cacheService = new Client();

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($cacheService);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'service' => 'my-service'
        ]);

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    public function testInvokeWithConnectionSettings()
    {
        $instance = $this->factory->__invoke($this->mockContainer, [
            'servers'      => [
                'tcp:/127.0.0.1:6379'
            ],
        ]);

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    public function testInvokeWithMultipleConnectionSettings()
    {
        $instance = $this->factory->__invoke($this->mockContainer, [
            'servers'      => [
                'tcp:/127.0.0.1:6379',
                'tcp:/127.0.0.1:6379',
            ],
        ]);

        $this->assertInstanceOf(PredisCachePool::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingServerAndService()
    {
        $this->factory->__invoke($this->mockContainer, []);
    }
}
