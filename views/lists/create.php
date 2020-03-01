<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $module wdmg\content\Module */
/* @var $model wdmg\content\models\Blocks */

$this->title = Yii::t('app/modules/content', 'Create a content list');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="content-lists-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small></h1>
</div>
<div class="content-lists-create">
    <?= $this->render('_form', [
        'model' => $model
    ]); ?>
</div>