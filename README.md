# Yii2 flysystem asset manager

Installation
------------
Either run
```
php composer.phar require --prefer-dist mikk150/yii2-asset-manager-flysystem "*"
```
or add
```
"mikk150/yii2-asset-manager-flysystem": "*"
```
to the require section of your `composer.json` file

Usage
-----
configure Yii2 config

```php
'components' => [
    'assetManager' => [
        'class' => mikk150\assetmanager\AssetManager::class,
        'basePath' => './',
        'baseUrl' => '//cdn.host.com',
        'flySystem' => [
            'class' => dosamigos\flysystem\FtpFsComponent::class, // or any other flysystem compatible filesystem that can be published on web easily
            'host' => 'cdn.host.com',
            'username' => 'cdn.username',
            'password' => 'cdn.password',
            'root' => 'www/'
        ]
    ],
]
```