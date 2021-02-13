<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\widgets\ListView;
?>

<?php Pjax::begin(); ?>
<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '<dl class="dl-horizontal">{items}</dl>{pager}',
    'itemView' => function($data, $key, $index, $widget) use ($model) {
        return Html::tag('dt', $data['label'] . "&nbsp;" . Html::tag('span', '[' . $data['name'] . ']', [
            'class' => "text-muted"
        ])) . Html::tag('dd', $data['content'], [
            'lang' => ($model->locale ?? Yii::$app->language)
        ]);
    }
]); ?>
<?php Pjax::end(); ?>
