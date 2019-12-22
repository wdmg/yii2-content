<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel wdmg\content\models\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/content', 'Content lists');
$this->params['breadcrumbs'][] = $this->title;

?>
    <div class="page-header">
        <h1>
            <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $module->version ?>]</small>
        </h1>
    </div>
    <div class="content-lists-index">
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
                        if ($fields = $data->getFields($data->fields))
                            return var_export($fields, true);
                        else
                            return $data->fields;
                    }
                ],
                'status',
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
                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <hr/>
        <?php Pjax::end(); ?>
    </div>

<?php echo $this->render('../_debug'); ?>