<?php
declare(strict_types=1);

namespace WShafer\PSR11PhpCache\Test;

use PHPUnit\Framework\TestCase;
use WShafer\PSR11PhpCache\Config\MainConfig;

class MainConfigTest extends TestCase
{
    /** @var MainConfig */
    protected $config;

    public function setup()
    {
        $configArray = $this->getConfigArray();

        $this->config = new MainConfig($configArray);

        $this->assertInstanceOf(MainConfig::class, $this->config);
    }

    public function getConfigArray()
    {
        return [
            'cacheOne' => [
                'type' => 'apc',
                'namespace' => 'One',
                'prefix' => 'one_',
                'logger' => 'loggerService',
                'options' => [
                    'optionOne' => true,
                ],
            ],

            'cacheTwo' => [
                'type' => 'apcu',
                'namespace' => 'Two',
                'prefix' => 'two_',
                'options' => [
                    'optionTwo' => true,
                ],
            ],

            'chained' => [
                'type' => 'chain',
                'options' => [
                    'caches' => [
                        'cacheOne',
                        'cacheTwo'
                    ],
                ],
            ],
        ];
    }

    public function testConstructor()
    {
    }

    public function testGetCacheConfig()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne'];

        $result = $this->config->getCacheConfig('cacheOne')->toArray();

        $this->assertEquals($expected, $result);
    }

    public function testGetType()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne']['type'];

        $result = $this->config->getCacheConfig('cacheOne')->getType();

        $this->assertEquals($expected, $result);
    }

    public function testGetOptions()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne']['options'];

        $result = $this->config->getCacheConfig('cacheOne')->getOptions();

        $this->assertEquals($expected, $result);
    }

    public function testGetNamespace()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne']['namespace'];

        $result = $this->config->getCacheConfig('cacheOne')->getNamespace();

        $this->assertEquals($expected, $result);
    }

    public function testGetPrefix()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne']['prefix'];

        $result = $this->config->getCacheConfig('cacheOne')->getPrefix();

        $this->assertEquals($expected, $result);
    }

    public function testGetLogger()
    {
        $configArray = $this->getConfigArray();
        $expected = $configArray['cacheOne']['logger'];

        $result = $this->config->getCacheConfig('cacheOne')->getLogger();

        $this->assertEquals($expected, $result);
    }
}
