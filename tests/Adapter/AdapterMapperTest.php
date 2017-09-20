<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

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

    public function setup()
    {
        $this->mapper = new AdapterMapper();
        $this->assertInstanceOf(AdapterMapper::class, $this->mapper);
    }

    public function testApc()
    {
        $result = $this->mapper->map('apc');
        $this->assertInstanceOf(ApcAdapterFactory::class, $result);
    }

    public function testApcu()
    {
        $result = $this->mapper->map('apcu');
        $this->assertInstanceOf(ApcuAdapterFactory::class, $result);
    }

    public function testArray()
    {
        $result = $this->mapper->map('array');
        $this->assertInstanceOf(ArrayAdapterFactory::class, $result);
    }

    public function testChain()
    {
        $result = $this->mapper->map('chain');
        $this->assertInstanceOf(ChainCacheAdapterFactory::class, $result);
    }

    public function testDoctrine()
    {
        $result = $this->mapper->map('doctrine');
        $this->assertInstanceOf(DoctrineCacheAdapterFactory::class, $result);
    }

    public function testFileSystem()
    {
        $result = $this->mapper->map('filesystem');
        $this->assertInstanceOf(FileSystemAdapterFactory::class, $result);
    }

    public function testIlluminate()
    {
        $result = $this->mapper->map('illuminate');
        $this->assertInstanceOf(IlluminateAdapterFactory::class, $result);
    }

    public function testMemcached()
    {
        $result = $this->mapper->map('memcached');
        $this->assertInstanceOf(MemcachedAdapterFactory::class, $result);
    }

    public function testMongo()
    {
        $result = $this->mapper->map('mongo');
        $this->assertInstanceOf(MongoAdapterFactory::class, $result);
    }

    public function testMongoDb()
    {
        $result = $this->mapper->map('mongodb');
        $this->assertInstanceOf(MongoAdapterFactory::class, $result);
    }

    public function testPredis()
    {
        $result = $this->mapper->map('predis');
        $this->assertInstanceOf(PredisAdapterFactory::class, $result);
    }

    public function testRedis()
    {
        $result = $this->mapper->map('redis');
        $this->assertInstanceOf(RedisAdapterFactory::class, $result);
    }

    public function testVoid()
    {
        $result = $this->mapper->map('void');
        $this->assertInstanceOf(VoidAdapterFactory::class, $result);
    }

    public function testNull()
    {
        $result = $this->mapper->map('nothere');
        $this->assertNull($result);
    }
}
