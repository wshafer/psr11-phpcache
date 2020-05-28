<?php

declare(strict_types=1);

namespace WShafer\PSR11PhpCacheTests\Config;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11PhpCache\Config\Config;
use WShafer\PSR11PhpCache\Exception\MissingCacheConfigException;

/**
 * @covers \WShafer\PSR11PhpCache\Config\Config
 */
class ConfigTest extends TestCase
{
    protected $config;

    protected function setUp(): void
    {
        $this->config = new Config(
            [
                'type' => 'array',
                'options' => [
                    'key' => 'value'
                ],
                'namespace' => 'namespace',
                'prefix' => 'prefix',
                'logger' => 'loggerServiceName'
            ]
        );
    }

    public function testGetType(): void
    {
        $result = $this->config->getType();

        $this->assertEquals('array', $result);
    }

    public function testGetTypeNotFound(): void
    {
        $this->expectException(MissingCacheConfigException::class);
        $config = new Config([]);
        $config->getType();
    }

    public function testGetOptions(): void
    {
        $result = $this->config->getOptions();

        $this->assertEquals(['key' => 'value'], $result);
    }

    public function testGetOptionsDefault(): void
    {
        $config = new Config([]);
        $result = $config->getOptions();

        $this->assertEmpty($result);
    }

    public function testGetNamespace(): void
    {
        $result = $this->config->getNamespace();

        $this->assertEquals('namespace', $result);
    }

    public function testGetNamespaceDefault(): void
    {
        $config = new Config([]);
        $result = $config->getNamespace();

        $this->assertNull($result);
    }

    public function testGetPrefix(): void
    {
        $result = $this->config->getPrefix();

        $this->assertEquals('prefix', $result);
    }

    public function testGetPrefixDefault(): void
    {
        $config = new Config([]);
        $result = $config->getPrefix();

        $this->assertNull($result);
    }

    public function testGetLogger(): void
    {
        $result = $this->config->getLoggerServiceName();

        $this->assertEquals('loggerServiceName', $result);
    }

    public function testGetLoggerDefault(): void
    {
        $config = new Config([]);
        $result = $config->getLoggerServiceName();

        $this->assertNull($result);
    }
}
