<?php

use wdmg\widgets\LangSwitcher;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model wdmg\content\models\Blocks */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="content-content-form">
    <?php
        echo LangSwitcher::widget([
            'label' => Yii::t('app/modules/content', 'Language version'),
            'model' => $block,
            'renderWidget' => 'button-group',
            'createRoute' => ['content/create', 'block_id' => $block->id],
            'updateRoute' => ['content/update', 'block_id' => $block->id],
            'supportLocales' => $this->context->module->supportLocales,
            'versions' => (isset($block->source_id)) ? $block->getAllVersions($block->source_id, true) : $block->getAllVersions($block->id, true),
            'options' => [
                'id' => 'locale-switcher',
                'class' => 'pull-right'
            ]
        ]);
    ?>
    <?php $form = ActiveForm::begin([
        'id' => "addContentForm"
    ]); ?>
    <?php
        foreach ($model->getAttributes() as $attribute => $value) {
            $label = $model->getAttributeLabel($attribute);
            echo $form->field($model, $attribute)->label($label . ' <span class="text-muted">[' .$attribute. ']</span>');
        }
    ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['content/index', 'block_id' => $block->id], ['class' => 'btn btn-default pull-left']) ?>
        <?= Html::submitButton(Yii::t('app/modules/content', 'Save'), ['class' => 'btn btn-save btn-success pull-right']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>