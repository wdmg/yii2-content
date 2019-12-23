[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.20-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-content/total.svg)](https://GitHub.com/wdmg/yii2-content/releases/)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-content.svg)](https://github.com/wdmg/yii2-content/blob/master/LICENSE)
![GitHub release](https://img.shields.io/github/release/wdmg/yii2-content/all.svg)

# Yii2 Content
Content manager for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.20 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 SelectInput](https://github.com/wdmg/yii2-selectinput) widget (required)

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-content"`

After configure db connection, run the following command in the console:

`$ php yii content/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-content/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'content' => [
            'class' => 'wdmg\content\Module',
            'routePrefix' => 'admin'
        ],
        ...
    ],


# Usage examples
To get the content you may use the component method Yii::$app->content->get() with `id` or `alias` of block/list name.

**Content block**

    <?php
        if ($block = Yii::$app->content->get(1)) {
            
            // Raw output
            echo $block['contact-email']; // where `contact-email` as filed name
            echo $block['contact-phone']; // where `contact-phone` as filed name
            
            // With ListView and ArrayDataProvider
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => Yii::$app->content->get(2)
            ]);
            echo yii\widgets\ListView::widget([
                'dataProvider' => $dataProvider,
                'layout' => '<dl class="dl-horizontal">{items}</dl>{pager}',
                'itemView' => function($data, $key, $index, $widget) {
                    return "<dt>" . $key . "</dt>" . "<dd>" . $data . "</dd>";
                }
            ]);
            
        }
    ?>

**Content list**

    <?php
        if ($list = Yii::$app->content->get('test-table')) {
            
            // Raw output
            foreach ($list as $row) {
                echo $row['contact-email']; // where `contact-email` as filed name
                echo $row['contact-phone']; // where `contact-phone` as filed name
            }
            
            // With GridView and ArrayDataProvider
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => Yii::$app->content->get('test-table')
            ]);
            echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
            ]);
            
        }
    ?>

# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('content')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.1.0.0 - Added content component and preview of content blocks/lists