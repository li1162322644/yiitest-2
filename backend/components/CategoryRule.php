<?php
/**
 * Created by PhpStorm.
 * User: G-emall
 * Date: 2017/8/30
 * Time: 18:40
 */

namespace backend\components;

use yii\rbac\Rule;

class CategoryRule extends Rule
{
    public $name = 'article';

    public function execute($user, $item, $params)
    {
        // 这里先设置为false,逻辑上后面再完善
        return true;
    }
}