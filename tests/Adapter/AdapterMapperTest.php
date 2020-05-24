<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11PhpCache\Adapter\AdapterMapper;
use WShafer\PSR11PhpCache\Adapter\ApcAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\ApcuAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\ArrayAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\ChainCacheAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\DoctrineCacheAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\FileSystemAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\IlluminateAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\MemcachedAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\MongoAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\PredisAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\RedisAdapterFactory;
use WShafer\PSR11PhpCache\Adapter\VoidAdapterFactory;

class AdapterMapperTest extends TestCase
{
    /**
     * @var AdapterMapper
     */
    protected $mapper;

    protected function setup(): void
    {
        $this->mapper = new AdapterMapper();
        $this->assertInstanceOf(AdapterMapper::class, $this->mapper);
    }

    public function testApc(): void
    {
        $result = $this->mapper->map('apc');
        $this->assertInstanceOf(ApcAdapterFactory::class, $result);
    }

    public function testApcu(): void
    {
        $result = $this->mapper->map('apcu');
        $this->assertInstanceOf(ApcuAdapterFactory::class, $result);
    }

    public function testArray(): void
    {
        $result = $this->mapper->map('array');
        $this->assertInstanceOf(ArrayAdapterFactory::class, $result);
    }

    public function testChain(): void
    {
        $result = $this->mapper->map('chain');
        $this->assertInstanceOf(ChainCacheAdapterFactory::class, $result);
    }

    public function testDoctrine(): void
    {
        $result = $this->mapper->map('doctrine');
        $this->assertInstanceOf(DoctrineCacheAdapterFactory::class, $result);
    }

    public function testFileSystem(): void
    {
        $result = $this->mapper->map('filesystem');
        $this->assertInstanceOf(FileSystemAdapterFactory::class, $result);
    }

    public function testIlluminate(): void
    {
        $result = $this->mapper->map('illuminate');
        $this->assertInstanceOf(IlluminateAdapterFactory::class, $result);
    }

    public function testMemcached(): void
    {
        $result = $this->mapper->map('memcached');
        $this->assertInstanceOf(MemcachedAdapterFactory::class, $result);
    }

    public function testMongo(): void
    {
        $result = $this->mapper->map('mongo');
        $this->assertInstanceOf(MongoAdapterFactory::class, $result);
    }

    public function testMongoDb(): void
    {
        $result = $this->mapper->map('mongodb');
        $this->assertInstanceOf(MongoAdapterFactory::class, $result);
    }

    public function testPredis(): void
    {
        $result = $this->mapper->map('predis');
        $this->assertInstanceOf(PredisAdapterFactory::class, $result);
    }

    public function testRedis(): void
    {
        $result = $this->mapper->map('redis');
        $this->assertInstanceOf(RedisAdapterFactory::class, $result);
    }

    public function testVoid(): void
    {
        $result = $this->mapper->map('void');
        $this->assertInstanceOf(VoidAdapterFactory::class, $result);
    }

    public function testNull(): void
    {
        $result = $this->mapper->map('nothere');
        $this->assertNull($result);
    }
}
