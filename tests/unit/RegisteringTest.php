<?php

namespace tests\unit;

use Codeception\Test\Unit;
use dosamigos\flysystem\LocalFsComponent;
use League\Flysystem\Config;
use mikk150\assetmanager\AssetManager;
use tests\MemoryFilesystem;
use yii\base\InvalidConfigException;

class RegisteringTest extends Unit
{
    public function testTryingToLinkAssets()
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('Linking assets is not supported.');

        $flySystem = new MemoryFilesystem();

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'linkAssets' => true,
            'basePath' => 'test',
            'baseUrl' => 'http://cdn.test.org/test/test',
        ]);
    }

    public function testTryingToCreateWithoutBasePath()
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('basePath needs to be set.');

        $flySystem = new MemoryFilesystem();

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'baseUrl' => 'http://cdn.test.org/test/test',
        ]);
    }

    public function testTryingToCreateWithoutBaseUrl()
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('baseUrl needs to be set.');

        $flySystem = new MemoryFilesystem();

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'basePath' => 'test',
        ]);
    }

    public function testCreatingWithoutFlySystem()
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The required component is not specified.');

        $assetManager = new AssetManager([
            'basePath' => 'test',
        ]);
    }

    public function testRegisteringSingleFile()
    {
        $flySystem = new MemoryFilesystem();

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'basePath' => 'test',
            'baseUrl' => 'http://cdn.test.org/test/test',
            'hashCallback' => function ($src) {
                return 'test';
            }
        ]);

        $asset = $assetManager->publish(codecept_data_dir() . '/file.js');

        $this->assertTrue($flySystem->has('test/test/file.js'));
        $this->assertEquals('http://cdn.test.org/test/test/test/file.js', $asset[1]);
    }

    public function testRegisteringDirectory()
    {
        $flySystem = new MemoryFilesystem();

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'basePath' => 'test',
            'baseUrl' => 'http://cdn.test.org/test/test',
            'hashCallback' => function ($src) {
                return 'test';
            }
        ]);

        $asset = $assetManager->publish(codecept_data_dir() . '/directory');

        $this->assertTrue($flySystem->has('test/test'));
        $this->assertTrue($flySystem->has('test/test/file.js'));
        $this->assertTrue($flySystem->has('test/test/icon.jpg'));
        $this->assertTrue($flySystem->has('test/test/subdirectory/style.css'));

        $this->assertEquals('http://cdn.test.org/test/test/test', $asset[1]);
    }

    public function testUpdatingFileWhenItWasPreviouslyPublished()
    {
        $flySystem = new MemoryFilesystem();

        $flySystem->write('test/test/file.js', 'old_content', [
            'timestamp' => 0
        ]);

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'basePath' => 'test',
            'baseUrl' => 'http://cdn.test.org/test/test',
            'hashCallback' => function ($src) {
                return 'test';
            }
        ]);

        $asset = $assetManager->publish(codecept_data_dir() . '/file.js');

        $this->assertTrue($flySystem->has('test/test/file.js'));
        $this->assertEquals('http://cdn.test.org/test/test/test/file.js', $asset[1]);
    }

    public function testUpdatingDirWhenItWasPreviouslyPublished()
    {
        $flySystem = new MemoryFilesystem();

        $flySystem->createDir('test/test');
        $flySystem->write('test/test/deleteme.js', 'old_content', [
            'timestamp' => 0
        ]);

        $assetManager = new AssetManager([
            'flySystem' => $flySystem,
            'basePath' => 'test',
            'baseUrl' => 'http://cdn.test.org/test/test',
            'forceCopy' => true,
            'hashCallback' => function ($src) {
                return 'test';
            }
        ]);

        $asset = $assetManager->publish(codecept_data_dir() . '/directory');

        $this->assertFalse($flySystem->has('test/test/deleteme.js'));
        $this->assertTrue($flySystem->has('test/test/file.js'));
        $this->assertEquals('http://cdn.test.org/test/test/test', $asset[1]);
    }
}
