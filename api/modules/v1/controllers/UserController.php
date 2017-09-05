<?php

namespace api\modules\v1\controllers;

use api\models\User;
use api\models\LoginForm;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\ArrayHelper;

class UserController extends \yii\rest\ActiveController
{
    public $modelClass = 'api\models\User';

    /**
     * @return array
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => HttpBearerAuth::className(),
                'optional' => [
                    'login',
                    'signup-test',
//                    'user-profile',
                ],
            ]
        ]);
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSignupTest()
    {
        $user = new User();
        $user->generateAuthKey();
        $user->setPassword('123456');
        $user->username = '111ds';
        $user->email = '111sd@111.com';
        $user->save(false);
        return ['code' => 0];
    }

    /**
     * 登录
     */
    public function actionLogin()
    {
        $model = new LoginForm;
        $model->setAttributes(Yii::$app->request->post());
        if ($user = $model->login()) {
            return $user->api_token;
        } else {
            return $model->errors;
        }
    }

    /**
     * 获取用户信息
     */
    public function actionUserProfile()
    {
        $user = $this->authenticate(Yii::$app->user, Yii::$app->request, Yii::$app->response);
//        $user = Yii::$app->user->identityClass;
        return [ 'id' => $user->id, 'username' => $user->username, 'email' => $user->email, ];
    }


}
