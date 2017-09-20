<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\MongoDB\MongoDBCachePool;
use MongoDB\Client;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\MemcachedAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\MongoAdapterFactory;

class MongoAdapterFactoryTest extends TestCase
{
    /** @var MemcachedAdapterFactory */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    public function setup()
    {
        if (!extension_loaded('mongodb')) {
            $this->markTestSkipped('mongodb extension not installed.  Skipping test');
        }

        if (!class_exists(Collection::class)) {
            $this->markTestSkipped('composer package mongodb/mongodb not installed.  Skipping test');
        }

        $this->mockContainer = $this->createMock(ContainerInterface::class);

        $this->factory = new MongoAdapterFactory();

        $this->assertInstanceOf(MongoAdapterFactory::class, $this->factory);
    }

    public function testInvokeWithService()
    {
        $collection = (new Client())->unitTest->fakeCollection;

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($collection);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'service' => 'my-service'
        ]);

        $this->assertInstanceOf(MongoDBCachePool::class, $instance);
    }

    public function testInvokeUsingConfig()
    {
        $instance = $this->factory->__invoke($this->mockContainer, [
            'dsn'        => 'mongodb://127.0.0.1',
            'database'   => 'fromconfig',
            'collection' => 'testOne'
        ]);

        $this->assertInstanceOf(MongoDBCachePool::class, $instance);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingServiceAndDsn()
    {
        $this->factory->__invoke($this->mockContainer, []);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingDbName()
    {
        $this->factory->__invoke($this->mockContainer, [
            'dsn'        => 'mongodb://127.0.0.1',
            'collection' => 'testOne'
        ]);
    }

    /**
     * @expectedException \WShafer\PSR11PhpCache\Exception\InvalidConfigException
     */
    public function testInvokeMissingCollectionName()
    {
        $this->factory->__invoke($this->mockContainer, [
            'dsn'        => 'mongodb://127.0.0.1',
            'database'   => 'fromconfig',
        ]);
    }
}
