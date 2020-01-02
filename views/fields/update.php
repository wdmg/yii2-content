<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $module wdmg\content\Module */
/* @var $model wdmg\content\models\Blocks */

$this->title = Yii::t('app/modules/content', 'Field: {label}', [
    'label' => $model->label,
]);

if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
else
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content blocks'), 'url' => ['blocks/index']];

$this->params['breadcrumbs'][] = ['label' => $block->title, 'url' => ['fields/index', 'block_id' => $block->id]];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="content-fields-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small></h1>
</div>
<div class="content-fields-update">
    <?= $this->render('_form', [
        'model' => $model,
        'block' => $block
    ]) ?>
</div>