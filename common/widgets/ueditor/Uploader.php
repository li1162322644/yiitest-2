<?php
namespace common\widgets\ueditor;

use yii;
use common\vendor\qiniu\Upload;

/**
 * Class Uploader
 * @package common\widgets\ueditor
 * 普通调用
 * //file 上传的表单里的文件名称，config配置
 * $uploadArr=new Uploader('file',$config,"upload",'att');
 * $upArr=$uploadArr->getFileInfo();//获取当前上传文件的详细信息
 *
 */

class Uploader
{
    private $fileField; //文件域名
    private $file; //文件上传对象
    private $config=[
        "pathFormat" => '/ueditor/php/upload/image/{yyyy}{mm}{dd}/{time}{rand:6}',
        "maxSize" => 3145728,
        "allowFiles" => [".png", ".jpg", ".jpeg", ".gif", ".bmp"]
      ]; //配置信息
    private $oriName; //原始文件名
    private $fileName; //新文件名
    private $fullName; //完整文件名,即从当前配置目录开始的URL
    private $fileSize; //文件大小
    private $fileType; //文件类型
    private $stateInfo; //上传状态信息,
    private $stateMap = array( //上传状态映射表，国际化用户需考虑此处数据的国际化
        "SUCCESS", //上传成功标记，在UEditor中内不可改变，否则flash判断会出错
        "文件大小超出 upload_max_filesize 限制",
        "文件大小超出 MAX_FILE_SIZE 限制",
        "文件未被完整上传",
        "没有文件被上传",
        "上传文件为空",
        "ERROR_TMP_FILE" => "临时文件错误",
        "ERROR_TMP_FILE_NOT_FOUND" => "找不到临时文件",
        "ERROR_SIZE_EXCEED" => "文件大小超出网站限制",
        "ERROR_TYPE_NOT_ALLOWED" => "文件类型不允许",
        "ERROR_CREATE_DIR" => "目录创建失败",
        "ERROR_DIR_NOT_WRITEABLE" => "目录没有写权限",
        "ERROR_FILE_MOVE" => "文件保存时出错",
        "ERROR_FILE_NOT_FOUND" => "找不到上传文件",
        "ERROR_WRITE_CONTENT" => "写入文件内容错误",
        "ERROR_UNKNOWN" => "未知错误",
        "ERROR_DEAD_LINK" => "链接不可用",
        "ERROR_HTTP_LINK" => "链接不是http链接",
        "ERROR_HTTP_CONTENTTYPE" => "链接contentType不正确"
    );
    /**
     *  上传的域名，默认hotel.domain,或者img.domain
     * @var string
     */
    public $url;

    /**
     * 构造函数
     * @param string $fileField  表单名称
     * @param array $config 配置项
     * @param string $type 是否解析base64编码，可省略。若开启，则$fileField代表的是base64编码的字符串表单名
     * @param string $url 上传的域名，默认hotel.domain,或者img.domain
     */
    public function __construct($fileField, $config='', $type = "upload",$url='hotel')
    {
        $this->fileField = $fileField;
        $this->config = !empty($config) ? $config : $this->config;
        $this->type = $type;
        $this->url = $url;
        if ($type == "base64") {
            $this->upBase64();
        }elseif($type == "ftp"){
            $this->ftpFile();
        } else {
            $this->upFile();
        }
        //        $this->stateMap['ERROR_TYPE_NOT_ALLOWED'] = iconv('unicode', 'utf-8', $this->stateMap['ERROR_TYPE_NOT_ALLOWED']);
    }

    /**
     * 上传文件的主处理方法
     * @return mixed
     */
    private function upFile()
    {
        $file = $this->file = isset($_FILES[$this->fileField])?$_FILES[$this->fileField]:$_FILES['file'];
        if (!$file) {
            $this->stateInfo = $this->getStateInfo("ERROR_FILE_NOT_FOUND");
            return;
        }
        if ($this->file['error']) {
            $this->stateInfo = $this->getStateInfo($file['error']);
            return;
        } else if (!file_exists($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMP_FILE_NOT_FOUND");
            return;
        } else if (!is_uploaded_file($file['tmp_name'])) {
            $this->stateInfo = $this->getStateInfo("ERROR_TMPFILE");
            return;
        }
        $this->oriName = $file['name'];
        $this->fileSize = $file['size'];
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->fileName = $this->getFileName();
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        //检查是否不允许的文件格式
        if (!$this->checkType()) {
            $this->stateInfo = $this->getStateInfo("ERROR_TYPE_NOT_ALLOWED");
            return;
        }
        /**
         * @var $upload \common\vendor\qiniu\Upload
         */
        $upload = Yii::$app->upload->init($this->url);
        $token = $upload->getToken();
        $rs = $upload->getUploadManager()->putFile($token, $this->fullName, $file['tmp_name']);
        if (isset($rs[0]['hash'])) {
            $this->stateInfo = $this->stateMap[0];
        } else {
            $this->stateInfo = $rs;
        }
    }

    /**
     * 处理base64编码的图片上传
     * @return mixed
     */
    private function upBase64()
    {
        $base64Data = $_POST[$this->fileField];
        $img = base64_decode($base64Data);
        $this->oriName = $this->config['oriName'];
        $this->fileSize = strlen($img);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        $this->fileName = $this->getFileName();
        //检查文件大小是否超出限制
        if (!$this->checkSize()) {
            $this->stateInfo = $this->getStateInfo("ERROR_SIZE_EXCEED");
            return;
        }
        $upload = Yii::$app->upload->init();
        $token = $upload->getToken();
        $rs = $upload->getUploadManager()->put($token,$this->fullName,$img);
        if (isset($rs[0]['hash'])) {
            $this->stateInfo = $this->stateMap[0];
        } else {
            $this->stateInfo = $rs;
        }
    }
    /**
     * 上传远程图片的主处理方法
     * @return mixed
     */
    private function ftpFile()
    {
        $this->oriName=basename($_FILES['tmp_name']);
        $this->fileType = $this->getFileExt();
        $this->fullName = $this->getFullName();
        /**
         * @var $upload \common\vendor\qiniu\Upload
         */
        $upload = Yii::$app->upload->init($this->url);
        $token = $upload->getToken();
        $rs = $upload->getUploadManager()->ftpFile($token, $this->fullName, $_FILES['tmp_name']);
        if (isset($rs[0]['hash'])) {
            $this->stateInfo = $this->stateMap[0];
        } else {
            $this->stateInfo = $rs;
        }
    }
    /**
     * 上传错误检查
     * @param $errCode
     * @return string
     */
    private function getStateInfo($errCode)
    {
        return !$this->stateMap[$errCode] ? $this->stateMap["ERROR_UNKNOWN"] : $this->stateMap[$errCode];
    }

    /**
     * 获取文件扩展名
     * @return string
     */
    private function getFileExt()
    {
        return strtolower(strrchr($this->oriName, '.'));
    }

    /**
     * 重命名文件
     * @return string
     */
    private function getFullName()
    {
        $format = Yii::$app->upload->init($this->url)->getPrefix().str_replace('.', '', date('YmdHis') . uniqid('', true));
        $ext = $this->getFileExt();
        return $format . $ext;
    }

    /**
     * 获取文件名
     * @return string
     */
    private function getFileName()
    {
        return $this->fullName;
    }


    /**
     * 文件类型检测
     * @return bool
     */
    private function checkType()
    {
        return in_array($this->getFileExt(), $this->config["allowFiles"]);
    }

    /**
     * 文件大小检测
     * @return bool
     */
    private function checkSize()
    {
        return $this->fileSize <= ($this->config["maxSize"]);
    }

    /**
     * 获取当前上传成功文件的各项信息
     * @return array
     */
    public function getFileInfo()
    {
        return array(
            "state" => $this->stateInfo,
            "url" => Yii::$app->upload->init($this->url)->domain.'/'.$this->fullName,
            "fullName"=>$this->fullName,
            "title" => $this->fileName,
            "original" => $this->oriName,
            "type" => $this->fileType,
            "size" => $this->fileSize
        );
    }
}
