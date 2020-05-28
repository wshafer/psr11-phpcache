<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\MongoDB\MongoDBCachePool;
use MongoDB\Client;
use MongoDB\Collection;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\MemcachedAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\MongoAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\MongoAdapterFactory
 */
class MongoAdapterFactoryTest extends TestCase
{
    /** @var MemcachedAdapterFactory */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    protected function setup(): void
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

    public function testInvokeWithService(): void
    {
        $collection = (new Client())->unitTest->fakeCollection;

        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($collection);

        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'service' => 'my-service'
            ]
        );

        $this->assertInstanceOf(MongoDBCachePool::class, $instance);
    }

    public function testInvokeUsingConfig(): void
    {
        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'dsn' => 'mongodb://127.0.0.1',
                'database' => 'fromconfig',
                'collection' => 'testOne'
            ]
        );

        $this->assertInstanceOf(MongoDBCachePool::class, $instance);
    }

    public function testInvokeMissingServiceAndDsn(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke($this->mockContainer, []);
    }

    public function testInvokeMissingDbName(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke(
            $this->mockContainer,
            [
                'dsn' => 'mongodb://127.0.0.1',
                'collection' => 'testOne'
            ]
        );
    }

    public function testInvokeMissingCollectionName(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->factory->__invoke(
            $this->mockContainer,
            [
                'dsn' => 'mongodb://127.0.0.1',
                'database' => 'fromconfig',
            ]
        );
    }
}
