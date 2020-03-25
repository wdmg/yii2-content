<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use wdmg\widgets\SelectInput;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\content\models\BlocksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/content', 'Fields for: {title}', [
    'title' => $block->title
]);

if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
else
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content blocks'), 'url' => ['blocks/index']];

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small>
    </h1>
</div>
<div class="content-fields-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'label',
            'name',
            'type',
            'sort_order',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/content', 'Actions'),
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'urlCreator' => function ($action, $data, $key, $index) use ($model, $block) {

                    if ($action === 'update')
                        return \yii\helpers\Url::toRoute(['fields/update', 'id' => $key, 'block_id' => $block->id]);

                    if ($action === 'delete')
                        return \yii\helpers\Url::toRoute(['fields/delete', 'id' => $key, 'block_id' => $block->id]);

                },
                'visibleButtons' => [
                    'view' => false
                ]
            ],
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/content', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/content', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/content', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/content', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?php
            if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
                echo Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['lists/index'], ['class' => 'btn btn-default pull-left']);
            else
                echo Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['blocks/index'], ['class' => 'btn btn-default pull-left']);
        ?>&nbsp;
        <?= Html::a(Yii::t('app/modules/content', 'Add new field'), ['fields/create', 'block_id' => $block->id], ['class' => 'btn btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>