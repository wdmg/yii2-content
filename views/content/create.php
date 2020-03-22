<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $module wdmg\content\Module */
/* @var $model wdmg\content\models\Blocks */

$this->title = Yii::t('app/modules/content', 'Add a row for: {title}', [
    'title' => $block->title
]);

$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', $block->title), 'url' => ['content/index', 'block_id' => $block->id]];
$this->params['breadcrumbs'][] = Yii::t('app/modules/content', 'New row');

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small></h1>
</div>
<div class="content-content-create">
    <?= $this->render('_form', [
        'model' => $model,
        'block' => $block
    ]); ?>
</div>