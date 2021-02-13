<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
?>

<?php Pjax::begin(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $index, $widget, $grid) {
        return [
            'lang' => ($model->locale ?? Yii::$app->language)
        ];
    },
]); ?>
<?php Pjax::end(); ?>
