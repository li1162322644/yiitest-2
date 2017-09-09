# Ueditor-七牛
# 一、composer七牛的官方sdk
composer require qiniu/php-sdk
# 二、编写七牛的集成类包，并注册为组件
在main.php 中注册
```php

defined('IMG_DOMAIN') or define('IMG_DOMAIN', '**');
defined('GEMALL_IMG_DOMAIN') or define('GEMALL_IMG_DOMAIN', '**');//用与其他平台的共享图片

//七牛云文件上传类，外链默认域名  http://oa4xtcdk1.bkt.clouddn.com
        'upload' => [
            'class' => 'common\vendor\qiniu\Upload',
            'url' => 'http://up-z2.qiniu.com',
            'buckets' => [
                ['name' => 'hotel', 'domain' => IMG_DOMAIN],
                ['name' => 'gemall', 'domain' => GEMALL_IMG_DOMAIN],
            ],
            'accessKey' => '**',
            'secretKey' => '**',
        ],
```
# 三、编写七牛管理类
namespace common\widgets\qiniu;

# 四、Ueditor组件下的UeditorAciton和Uploader类的改写（因为要对其源码进行改写，建议不要composer安装，如果非要的话，建议重写这两个类）
UeditorAction主要是修改获取获取已上传的方法，改成从七牛获取上传文件列表
Uploader主要是修改几种上传文件列表的方法，上传到七牛空间中

# 五、或者还可以直接挑用Ueditor组件中的Uploader上传到七牛云
```php 
    $uploadArr = new Uploader('AvatarForm','','upload','gemall');//参数可参考构造方法
    $upArr = $uploadArr->getFileInfo();
```
