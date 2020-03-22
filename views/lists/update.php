<?php
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $module wdmg\content\Module */
/* @var $model wdmg\content\models\Blocks */

$this->title = Yii::t('app/modules/content', 'Updating list: {title}', [
    'title' => $model->title,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/pages', 'Edit list');

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small></h1>
</div>
<div class="content-lists-update">
    <?= $this->render('_form', [
        'model' => $model
    ]) ?>
</div>