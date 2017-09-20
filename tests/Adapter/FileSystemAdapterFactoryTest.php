<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test\Adapter;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Adapter\FileSystemAdapterFactory;

class FileSystemAdapterFactoryTest extends TestCase
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var \PHPUnit_Framework_MockObject_MockObject|Filesystem */
    protected $mockFileSystem;

    public function setup()
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockFileSystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new FileSystemAdapterFactory();

        $this->assertInstanceOf(FileSystemAdapterFactory::class, $this->factory);
    }

    public function testInvoke()
    {
        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($this->mockFileSystem);

        $instance = $this->factory->__invoke($this->mockContainer, [
            'flySystemService' => 'my-service',
            'folder' => 'some-folder'
        ]);

        $this->assertInstanceOf(FilesystemCachePool::class, $instance);
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
            'flySystemService' => 'my-service',
            'folder' => 'some-folder'
        ]);
    }
}
