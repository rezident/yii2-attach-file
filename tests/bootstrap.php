<?php

use rezident\attachfile\AttachFileModule;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$application = new yii\console\Application([
    'id' => 'yii-console',
    'basePath' => __DIR__ . '/../src',
    'controllerNamespace' => 'yii\console\controllers',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=192.168.33.13;dbname=attach',
            'username' => 'root',
            'password' => 'vagrant',
            'charset' => 'utf8'
        ]
    ],

    'modules' => [
        'attach_file' => [
            'class' => AttachFileModule::class,
            'originalsPath' => '@app/files/originals',
            'viewsPath' => '@app/files/views',
            'webPrefix' => '/files'
        ]
    ]
]);

$application->setVendorPath(__DIR__ . '/../vendor');
//$application->run();