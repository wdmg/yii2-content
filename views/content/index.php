<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use wdmg\widgets\SelectInput;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\content\models\BlocksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
    $this->title = Yii::t('app/modules/content', 'List of content: {title}', [
        'title' => $block->title
    ]);
else
    $this->title = Yii::t('app/modules/content', 'Content block: {title}', [
        'title' => $block->title
    ]);

if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content lists'), 'url' => ['lists/index']];
else
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/content', 'Content blocks'), 'url' => ['blocks/index']];

if ($block->title)
    $this->params['breadcrumbs'][] = $block->title;
else
    $this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small>
    </h1>
</div>
<div class="content-content-index">
    <?php Pjax::begin(); ?>
    <?php
        if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
                'columns' => \yii\helpers\ArrayHelper::merge(
                    [
                        [
                            'class' => 'yii\grid\SerialColumn'
                        ]
                    ],
                    $columns,
                    [
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header' => Yii::t('app/modules/content', 'Actions'),
                            'contentOptions' => [
                                'class' => 'text-center',
                                'style' => 'min-width:120px',
                            ],
                            'visibleButtons' => [
                                'view' => false
                            ],
                            'urlCreator' => function($action, $model, $key, $index) use ($block, $items) {

                                if ($action == 'update' && isset($items[$key]))
                                    return ['content/update', 'block_id' => $block->id, 'row_order' => $items[$key]];
                                elseif ($action == 'delete' && isset($items[$key]))
                                    return ['content/delete', 'block_id' => $block->id, 'row_order' => $items[$key]];

                            },
                        ]
                    ]
                ),
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
            ]);
        } else {
            echo GridView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'field',
                        'filter' => true,
                        'value' => function($data) {
                            if ($data->field->label) {
                                return $data->field->label;
                            } else {
                                return $data->field_id;
                            }
                        }
                    ],
                    'content'
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
            ]);
        }
    ?>
    <hr/>
    <div>
        <?php
        if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type)
            echo Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['lists/index'], ['class' => 'btn btn-default pull-left']);
        else
            echo Html::a(Yii::t('app/modules/content', '&larr; Back to list'), ['blocks/index'], ['class' => 'btn btn-default pull-left']);
        ?>&nbsp;

        <?php
        if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
            echo Html::a(Yii::t('app/modules/content', 'Add new row'), ['content/create', 'block_id' => $block->id], ['class' => 'btn btn-success pull-right']);
        } else {
            echo Html::a(Yii::t('app/modules/content', 'Delete content'), ['content/delete', 'block_id' => $block->id], ['class' => 'btn btn-danger pull-right']);
            echo Html::a(Yii::t('app/modules/content', 'Edit content'), ['content/update', 'block_id' => $block->id], ['class' => 'btn btn-success pull-right']);
        }
        ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>