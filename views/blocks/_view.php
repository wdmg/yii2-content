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
        return "<dt>" . $model->generateAttributeLabel($data["name"]) . "</dt>" . "<dd>" . $data["content"] . "</dd>";
    }
]); ?>
<?php Pjax::end(); ?>
