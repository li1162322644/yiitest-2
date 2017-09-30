<?php

namespace backend\controllers;

use backend\components\DownloadJob;
use common\components\Upload;
use common\models\BlogCategory;
use Yii;
use common\models\Blog;
use common\models\BlogSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * BlogController implements the CRUD actions for Blog model.
 */
class BlogController extends Controller
{
    //公共的图片上传
    public function actions()
    {
        return [
            'ueditor' => [
                'class' => 'common\widgets\ueditor\UeditorAction',
            ],
            'get-region' => [
                'class' => \chenkby\region\RegionAction::className(),
                'model' => \common\models\Blog::className()
            ]

        ];
    }

    public function allowActions()
    {
        return ['ueditor', 'site', 'get-region'];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Blog models.
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->queue->push(new DownloadJob([
            'a' => 1,
            'b' => 1,
        ]));
//        echo $id;die;
        $searchModel = new BlogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Blog model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Blog model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Blog();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $model->save(false);


                $transaction->commit();
                return $this->redirect(['index']);
            } catch (\ErrorException $e) {
                //回滚
                $transaction->rollback();
                throw $e;
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Blog model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save(false);

            $blogId = $model->id;
            BlogCategory::insertBlogCategory($blogId, $model->category);
            return $this->redirect(['index']);
        }
        $model->category = BlogCategory::getRelationCategory($id);
        return $this->render('update', [
            'model' => $model,
        ]);

    }

    /**
     * Deletes an existing Blog model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Blog model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Blog the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Blog::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionUpload()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new Upload();
            $info = $model->upImage();
            if ($info && is_array($info)) {
                return $info;
            } else {
                return ['code' => 1, 'msg' => 'error'];
            }
        } catch (\Exception $e) {
            return ['code' => 1, 'msg' => $e->getMessage()];
        }
    }

    /**
     * Function output the site that you selected.
     * @param int $pid
     * @param int $typeid
     */
    public function actionSite($pid, $typeid = 0)
    {
        $model = new Blog();
        $model = $model->getCityList($pid);

        if ($typeid == 1) {
            $aa = "--请选择市--";
        } else if ($typeid == 2 && $model) {
            $aa = "--请选择区--";
        }
        echo Html::tag('option', $aa, ['value' => 'empty']);
        foreach ($model as $value => $name) {
            echo Html::tag('option', Html::encode($name), array('value' => $value));
        }
    }
}
