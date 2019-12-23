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

$this->title = Yii::t('app/modules/content', 'Content blocks');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small>
    </h1>
</div>
<div class="content-blocks-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'title',
            'description',
            'alias',
            [
                'attribute' => 'fields',
                'format' => 'raw',
                'value' => function($data) {
                    if ($fields = $data->getFields($data->fields)) {
                        $list = [];
                        foreach ($fields as $field) {
                            $list[] = '<span class="label label-info">' . $field['label'] . '</span>';
                        }
                        return join($list, " ");
                    } else {
                        return $data->fields;
                    }
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::CONTENT_BLOCK_STATUS_PUBLISHED) {
                        return '<span class="label label-success">' . Yii::t('app/modules/content', 'Published') . '</span>';
                    } elseif ($data->status == $data::CONTENT_BLOCK_STATUS_DRAFT) {
                        return '<span class="label label-default">' . Yii::t('app/modules/content', 'Draft') . '</span>';
                    } else {
                        return $data->status;
                    }
                }
            ],
            'created_at',
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function($data) {
                    if ($data->created->id && $data->created->username)
                        return Html::a($data->created->username, ['../admin/users/view/', 'id' => $data->created->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    else
                        return $data->created_by;
                }
            ],
            'updated_at',
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function($data) {
                    if ($data->updated->id && $data->updated->username)
                        return Html::a($data->updated->username, ['../admin/users/view/', 'id' => $data->updated->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    else
                        return $data->updated_by;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/newsletters', 'Actions'),
                'contentOptions' => [
                    'class' => 'text-center',
                    'style' => 'min-width:120px',
                ],
                'buttons'=> [
                    'view' => function($url, $data, $key) {
                        $url = Url::toRoute(['blocks/view', 'id' => $key]);
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                            'class' => 'content-preview-link',
                            'title' => Yii::t('app/modules/content', 'Content preview'),
                            'data-toggle' => 'modal',
                            'data-target' => '#contentPreview',
                            'data-id' => $key,
                            'data-pjax' => '1'
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
    <hr/>
    <?php Pjax::end(); ?>
</div>

<?php $this->registerJs(<<< JS
    $('body').delegate('.content-preview-link', 'click', function(event) {
        event.preventDefault();
        $.get(
            $(this).attr('href'),
            function (data) {
                $('#contentPreview .modal-body').html(data);
                $('#contentPreview').modal();
            }  
        );
    });
JS
); ?>

<?php Modal::begin([
    'id' => 'contentPreview',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/content', 'Content preview').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../_debug'); ?>