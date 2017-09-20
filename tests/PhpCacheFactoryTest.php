<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test;

use Cache\Adapter\Apc\ApcCachePool;
use Cache\Adapter\PHPArray\ArrayCachePool;
use Cache\Adapter\Void\VoidCachePool;
use Cache\Namespaced\NamespacedCachePool;
use Cache\Prefixed\PrefixedCachePool;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WShafer\PSR11PhpCache\Adapter\ArrayAdapterFactory;
use WShafer\PSR11PhpCache\PhpCacheFactory;

class PhpCacheFactoryTest extends TestCase
{
    /** @var PhpCacheFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    protected $configArray = [
        'caches' => [
            'default' => [
                'type' => 'array'
            ],

            'cacheTwo' => [
                'type' => 'apc'
            ]
        ]
    ];

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new PhpCacheFactory();

        $this->assertInstanceOf(PhpCacheFactory::class, $this->factory);
    }

    public function testConstruct()
    {
    }

    public function testInvoke()
    {
        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    public function testInvokeSlim()
    {
        $map = [
            ['settings', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('settings')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    public function testInvokeSymfony()
    {
        $this->mockContainer = $this->createMock(ContainerBuilder::class);

        $this->mockContainer->expects($this->any())
            ->method('hasParameter')
            ->with('caches')
            ->willReturn(true);

        $this->mockContainer->expects($this->any())
            ->method('getParameter')
            ->with('caches')
            ->willReturn($this->configArray['caches']);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\MissingCacheConfigException
     */
    public function testInvokeNoConfig()
    {
        $map = [
            ['settings', false],
            ['config', false],
            ['void', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->factory->__invoke($this->mockContainer);
    }

    public function testInvokeWithoutMapper()
    {
        $this->configArray['caches']['default']['type'] = ArrayAdapterFactory::class;

        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    public function testInvokeWithCallableFactory()
    {
        $this->configArray['caches']['default']['type'] = function (ContainerInterface $container, $options) {
            $factory = new ArrayAdapterFactory();
            return $factory($container, $options);
        };

        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    public function testInvokeAddsLoggerService()
    {
        $this->configArray['caches']['default']['logger'] = 'my-logger';
        $mockLogger = $this->createMock(LoggerInterface::class);

        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
            ['my-logger', false],
        ];

        $mapGet = [
            ['config', $this->configArray],
            ['my-logger', $mockLogger],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($mapGet));

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(ArrayCachePool::class, $pool);
    }

    public function testInvokeWithNamespace()
    {
        $this->configArray['caches']['default']['namespace'] = 'my-namespace';

        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(NamespacedCachePool::class, $pool);
    }

    public function testInvokeWithPrefix()
    {
        $this->configArray['caches']['default']['prefix'] = 'my-prefix';

        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = $this->factory->__invoke($this->mockContainer);

        $this->assertInstanceOf(PrefixedCachePool::class, $pool);
    }

    public function testInvokeWithConfigKey()
    {
        $map = [
            ['settings', false],
            ['config', true],
            ['array', false],
        ];

        $this->mockContainer->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($map));

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('config')
            ->willReturn($this->configArray);

        $pool = PhpCacheFactory::cacheTwo($this->mockContainer);

        $this->assertInstanceOf(ApcCachePool::class, $pool);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidContainerException
     */
    public function testCallStaticNoContainer()
    {
        PhpCacheFactory::cacheTwo(new \stdClass());
    }
}
