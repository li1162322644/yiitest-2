<?php
/**
 * Created by PhpStorm.
 * User: G-emall
 * Date: 2017/9/30
 * Time: 13:42
 */

namespace backend\components;


use yii\base\Object;

class DownloadJob extends Object implements \yii\queue\Job
{
    public $a;
    public $b;
    public function execute($queue)
    {
//        file_put_contents($this->file, file_get_contents($this->url));
        echo $this->a + $this->b;

    }

}