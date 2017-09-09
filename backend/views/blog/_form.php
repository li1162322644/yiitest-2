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

    <?= $form->field($model,'content')->widget('common\widgets\ueditor\Ueditor',['options'=>['maximumWords'=>10000,'initialFrameWidth'=>1200,'initialFrameHeight'=>300]]); ?>

    <?= $form->field($model, 'views')->textInput() ?>

    <?= $form->field($model, 'is_delete')->radioList(Blog::dropDown('is_delete')) ?>

    <?= $form->field($model, 'category')->label('栏目')->checkboxList(Category::dropDownList()) ?>

    <?php
    echo $form->field($model, 'file')->widget('manks\FileInput', [
    ]);
    ?>





    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
