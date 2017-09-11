<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Category;
use common\models\Blog;

/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="blog-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor', ['options' => ['maximumWords' => 10000, 'initialFrameWidth' => 1200, 'initialFrameHeight' => 300]]); ?>

    <?= $form->field($model, 'views')->textInput() ?>

    <?= $form->field($model, 'is_delete')->radioList(Blog::dropDown('is_delete')) ?>

    <?= $form->field($model, 'category')->label('栏目')->checkboxList(Category::dropDownList()) ?>

    <?php
    echo $form->field($model, 'file')->widget('manks\FileInput', [
    ]);
    ?>

    <?/*= $form->field($model, 'province')->dropDownList($model->getCityList(0), [
        'prompt' => '--请选择省--',
        'onchange' => '
            $(".form-group.field-blog-area").hide();
            $.post("' . yii::$app->urlManager->createUrl('blog/site') . '?typeid=1&pid="+$(this).val(),function(data){
                $("select#blog-city").html(data);
            });',]) */?><!--

    <?/*= $form->field($model, 'city')->dropDownList($model->getCityList($model->province), [
        'prompt' => '--请选择市--',
        'onchange' => '
            $(".form-group.field-blog-area").show();
            $.post("' . yii::$app->urlManager->createUrl('blog/site') . '?typeid=2&pid="+$(this).val(),function(data){
                $("select#blog-area").html(data);
            });',]) */?>

    --><?/*= $form->field($model, 'area')->dropDownList($model->getCityList($model->city), ['prompt' => '--请选择区--',]) */?>

    <?= $form->field($model, 'district')->widget(\chenkby\region\Region::className(),[
        'model'=>$model,
        'url'=> \yii\helpers\Url::toRoute(['get-region']),
        'province'=>[
            'attribute'=>'province',
            'items'=>Blog::getRegion(),
            'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择省份']
        ],
        'city'=>[
            'attribute'=>'city',
            'items'=>Blog::getRegion($model['province']),
            'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择城市']
        ],
        'district'=>[
            'attribute'=>'district',
            'items'=>Blog::getRegion($model['city']),
            'options'=>['class'=>'form-control form-control-inline','prompt'=>'选择县/区']
        ]
    ]);
    ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
