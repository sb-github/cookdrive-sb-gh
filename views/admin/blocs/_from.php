<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form ActiveForm */
?>
<div class="admin-blocs-_from">

    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => 'sort']); ?>
    <div class="row">
        <?= $form->field($model, 'name') ?>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'startDate')->widget(DatePicker::className(), [
                'options' => ['readonly' => 'readonly'],
                'dateFormat' => 'yyyy-MM-dd',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'endDate')->widget(DatePicker::className(), [
                'options' => ['readonly' => 'readonly'],
                'dateFormat' => 'yyyy-MM-dd',
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= Html::submitButton('Сортування', ['class' => 'btn btn-primary pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div><!-- admin-blocs-_from -->
