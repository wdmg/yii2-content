<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\grid\GridView;
?>

<?php Pjax::begin(); ?>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
]); ?>
<?php Pjax::end(); ?>
