<?php
/**
 * 七牛云上传类
 *
 * sdk使用帮助：
 * http://developer.qiniu.com/code/v7/sdk/php.html
 */
namespace common\widgets\qiniu;
use Qiniu\Auth;
use Qiniu\Storage\BucketManager;
use Qiniu\Storage\UploadManager;
use yii;

class Upload
{
    /**
     * 存储空间名称
     * @var string
     */
    public $buckets = [];
    public $bucket;
    public $accessKey = '';
    public $secretKey = '';
    public $callbackUrl = '';
    public $expires = 3600;

    /**
     * 图片域名
     * @var string
     */
    public $domain = '';

    /**
     * 初始化配置
     * hotel 使用buckets的第一个配置，上传到 hotel.domain
     * gemall 使用buckets的第二个配置，上传到 gemall.domain
     * @param string $key "hotel" or "gemall"
     * @return $this
     */
    public function init($key='hotel'){
        //统一使用第一个img域名
/*        $this->bucket = $this->buckets[0]['name'];
        $this->domain = $this->buckets[0]['domain'];*/
        if($key=='hotel'){
            $this->bucket = $this->buckets[0]['name'];
            $this->domain = $this->buckets[0]['domain'];
        }else{
            $this->bucket = $this->buckets[1]['name'];
            $this->domain = $this->buckets[1]['domain'];
        }
        return $this;
    }

    /**
     * 获取token
     * @param null $policy
     * @param null $key
     * @param bool $strictPolicy
     * @return string
     */
    public function getToken($policy = null, $key = null, $strictPolicy = true)
    {
        $auth = new Auth($this->accessKey, $this->secretKey);
        if (!empty($this->callbackUrl)) {
            $uid = Yii::$app->getUser()->id;
            if (!$uid) $uid = 0;
            $policy = array(
                'callbackUrl' => $this->callbackUrl,
                'callbackBody' => '{"fsize":"$(fsize)", "fkey":"$(key)",  "uid":' . $uid . '}'
            );
        }
        return $auth->uploadToken($this->bucket, $key, $this->expires, $policy, $strictPolicy);
    }

    private static $_uploadManager = null;
    private static $_bucketManager = null;

    /**
     * 资源上传接口
     * @return UploadManager
     */
    public function getUploadManager()
    {
        if (!self::$_uploadManager) {
            self::$_uploadManager = new UploadManager();
        }
        return self::$_uploadManager;
    }

    /**
     * 空间资源管理及批量操作接口
     * @return null|BucketManager
     */
    public function getBucketManager(){
        if (!self::$_bucketManager) {
            $auth = new Auth($this->accessKey, $this->secretKey);
            self::$_bucketManager = new BucketManager($auth);
        }
        return self::$_bucketManager;
    }

    /**
     * 获取文件前缀
     * @return string
     */
    public function getPrefix(){
        $uid = Yii::$app->getUser()->id;
        if(!$uid) $uid = 0;
        if($this->domain==IMG_DOMAIN){
            $uid .= '_hotel';
        }else if ($this->domain==GEMALL_IMG_DOMAIN){
            $uid .= '_img';
        }
        return $uid.'_';
    }

}