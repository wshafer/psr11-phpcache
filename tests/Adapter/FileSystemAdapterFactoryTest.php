<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Adapter;

use Cache\Adapter\Filesystem\FilesystemCachePool;
use League\Flysystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;
use WShafer\PSR11PhpCache\Adapter\FactoryInterface;
use WShafer\PSR11PhpCache\Adapter\FileSystemAdapterFactory;
use PHPUnit\Framework\MockObject\MockObject;
use WShafer\PSR11PhpCache\Exception\InvalidConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Adapter\FileSystemAdapterFactory
 */
class FileSystemAdapterFactoryTest extends TestCase
{
    /** @var FactoryInterface */
    protected $factory;

    /** @var MockObject|ContainerInterface */
    protected $mockContainer;

    /** @var MockObject|Filesystem */
    protected $mockFileSystem;

    protected function setup(): void
    {
        $this->mockContainer = $this->createMock(ContainerInterface::class);
        $this->mockFileSystem = $this->getMockBuilder(Filesystem::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->factory = new FileSystemAdapterFactory();

        $this->assertInstanceOf(FileSystemAdapterFactory::class, $this->factory);
    }

    public function testInvoke(): void
    {
        $this->mockContainer->expects($this->once())
            ->method('get')
            ->with('my-service')
            ->willReturn($this->mockFileSystem);

        $instance = $this->factory->__invoke(
            $this->mockContainer,
            [
                'flySystemService' => 'my-service',
                'folder' => 'some-folder'
            ]
        );

        $this->assertInstanceOf(FilesystemCachePool::class, $instance);
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
                'flySystemService' => 'my-service',
                'folder' => 'some-folder'
            ]
        );
    }
}
