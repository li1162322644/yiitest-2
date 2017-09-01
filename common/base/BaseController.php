<?php
/**
 * Created by PhpStorm.
 * User: G-emall
 * Date: 2017/8/28
 * Time: 18:07
 */

namespace common\base;

use Yii;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\helpers\Json;

class BaseController extends Controller
{
    /**
     * 通用的modal ajax表单验证
     * @param  $model
     * @return void
     */
    public function performModalAjaxValidation($model)
    {

        if (Yii::$app->request->isAjax && Yii::$app->request->isPost) {
            /** @var \yii\db\ActiveRecord $model */
            $model->load($_POST);
            echo Json::encode(ActiveForm::validate($model));
            Yii::$app->end();
        }
    }
    /**
     * 通用的ajax表单验证
     * @param  $model
     * @return void
     */
    public function performAjaxValidation($model)
    {
        if (Yii::$app->request->isAjax) {
            /** @var \yii\db\ActiveRecord $model */
            $model->load($_POST);
            echo Json::encode(ActiveForm::validate($model));
            Yii::$app->end();
        }
    }
}