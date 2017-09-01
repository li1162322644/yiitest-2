<?php
/**
 *author:limin
 *creatime:2017/8/24 21:26
 *description:
 */

namespace frontend\controllers;

use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex($name)
    {
        return $this->render('index', ['name' => $name]);
    }

    public function actionCreate()
    {
    }
}