<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\content\models\Blocks */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="content-fields-form">
    <?php $form = ActiveForm::begin([
        'id' => "fieldForm",
        'enableAjaxValidation' => true
    ]); ?>
    <?= $form->field($model, 'label'); ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'type')->widget(SelectInput::class, [
        'items' => $model->getTypesList(false),
        'options' => [
            'class' => 'form-control'
        ]
    ]); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['fields/index', 'block_id' => $block->id], ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::submitButton(Yii::t('app/modules/content', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php $this->registerJs(<<< JS
$(document).ready(function() {
    function afterValidateAttribute(event, attribute, messages)
    {
        if (attribute.name && !attribute.alias && messages.length == 0) {
            var form = $(event.target);
            $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }
            ).done(function(data) {
                if (data.alias && form.find('#fields-name').val().length == 0) {
                    form.find('#fields-name').val(data.alias);
                    form.yiiActiveForm('validateAttribute', 'fields-name');
                }
            });
            return false;
        }
    }
    $("#fieldForm").on("afterValidateAttribute", afterValidateAttribute);
});
JS
); ?>