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
            [
                'attribute' => 'title',
                'format' => 'html',
                'value' => function($data) {
                    if ($data->title & $data->description) {
                        return '<b>' . $data->title . '</b><br/><span class="text-muted">' . mb_strimwidth(strip_tags($data->description), 0, 64, '…') . '</span>';
                    } elseif ($data->title) {
                        return '<b>' . $data->title . '</b>';
                    } else {
                        return null;
                    }
                }
            ],
            'alias',
            [
                'attribute' => 'fields',
                'format' => 'raw',
                'value' => function($data) {
                    $html = '';
                    if ($fields = $data->getFields($data->fields)) {
                        $list = [];
                        foreach ($fields as $field) {
                            $list[] = '<span class="label label-info">' . $field['label'] . '</span>';
                        }

                        $onMore = false;
                        if (count($list) > 5)
                            $onMore = true;

                        if ($onMore)
                            $html = join(array_slice($list, 0, 5), " ") . "&nbsp;… ";
                        else
                            $html = join($list, " ");

                    }

                    $html .= Html::a(
                        Yii::t('app/modules/content', 'Edit') . '&nbsp;<span class="glyphicon glyphicon-edit"></span>',
                        Url::toRoute(['fields/index', 'block_id' => $data->id]),
                        [
                            'class' => 'btn btn-link btn-sm btn-block',
                            'title' => Yii::t('app/modules/content', 'Edit fields'),
                            'data-pjax' => '0'
                        ]
                    );
                    return $html;
                }
            ],
            [
                'attribute' => 'content',
                'format' => 'raw',
                'value' => function($data) {
                    $html = '';

                    $html .= Html::a(
                        Yii::t('app/modules/content', 'Edit') . '&nbsp;<span class="glyphicon glyphicon-edit"></span>',
                        Url::toRoute(['content/index', 'block_id' => $data->id]),
                        [
                            'class' => 'btn btn-link btn-sm btn-block',
                            'title' => Yii::t('app/modules/content', 'Edit content'),
                            'data-pjax' => '0'
                        ]
                    );

                    $html .= Html::a(
                        Yii::t('app/modules/content', 'Preview') . '&nbsp;<span class="glyphicon glyphicon-eye-open"></span>',
                        Url::toRoute(['blocks/view', 'id' => $data->id]),
                        [
                            'class' => 'btn btn-link btn-sm btn-block content-preview-link',
                            'title' => Yii::t('app/modules/content', 'Content preview'),
                            'data-toggle' => 'modal',
                            'data-target' => '#contentPreview',
                            'data-id' => $data->id,
                            'data-pjax' => '0'
                        ]
                    );

                    return $html;
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

            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/content','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/content','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/content', 'Actions'),
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
                            'data-pjax' => '0'
                        ]);
                    },
                ]
            ],
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/content', 'Add new block'), ['blocks/create'], ['class' => 'btn btn-success pull-right']) ?>
    </div>
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