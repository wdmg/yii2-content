<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model wdmg\content\models\Blocks */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="content-content-form">
    <?php $form = ActiveForm::begin([
        'id' => "addContentForm"
    ]); ?>
    <?php
        foreach ($model->getAttributes() as $name => $value) {
            echo $form->field($model, $name);
        }
    ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['content/index', 'block_id' => $block->id], ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::submitButton(Yii::t('app/modules/content', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>