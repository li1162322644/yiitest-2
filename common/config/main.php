<?php

defined('IMG_DOMAIN') or define('IMG_DOMAIN', 'http://oolcrfhnn.bkt.clouddn.com');
defined('GEMALL_IMG_DOMAIN') or define('GEMALL_IMG_DOMAIN', 'http://imgnew.e-gatenet.cn');//用与其他平台的共享图片

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=advanced',
            'username' => 'root',
            'password' => 'root',
            'charset' => 'utf8',
            'enableSchemaCache' => true,
            'schemaCacheDuration' => 24 * 3600,
            'schemaCache' => 'cache',//表字段缓存
        ],
        //七牛云文件上传类，外链默认域名  http://oa4xtcdk1.bkt.clouddn.com
        'upload' => [
            'class' => 'common\widgets\qiniu\Upload',
            'url' => 'http://up-z2.qiniu.com',
            'buckets' => [
                ['name' => 'hotel', 'domain' => IMG_DOMAIN],
                ['name' => 'gemall', 'domain' => GEMALL_IMG_DOMAIN],
            ],
            'accessKey' => 'oAjSOSDllU5LC5ndpHJLS8FHZkYqHKAYghkWz6W7',
            'secretKey' => 'qRzWb4LxaP2NYEpaaWDj5rxzif1nlEO1q738A90x',
        ],

    ],
    'language' => 'zh-CN',
    'timeZone' => 'Asia/Chongqing',
];
