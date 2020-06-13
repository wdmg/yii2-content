[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.35-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Downloads](https://img.shields.io/packagist/dt/wdmg/yii2-content.svg)](https://packagist.org/packages/wdmg/yii2-content)
[![Packagist Version](https://img.shields.io/packagist/v/wdmg/yii2-content.svg)](https://packagist.org/packages/wdmg/yii2-content)
![Progress](https://img.shields.io/badge/progress-ready_to_use-green.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-content.svg)](https://github.com/wdmg/yii2-content/blob/master/LICENSE)

# Yii2 Content
Content manager for Yii2.
The module allows you to create multilingual content blocks, as well as lists that can be displayed in frontend with extension component.

This module is an integral part of the [Butterfly.SMS](https://butterflycms.com/) content management system, but can also be used as an standalone extension.

Copyrights (c) 2019-2020 [W.D.M.Group, Ukraine](https://wdmg.com.ua/)

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.35 and newest
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
            echo $block['adress']; // where `adress` as filed name
            echo $block['phone']; // where `phone` as filed name
            echo $block['email']; // where `email` as filed name
            
            // With ListView and ArrayDataProvider
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $block
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
        if ($list = Yii::$app->content->get('our-team', 'ru-RU')) {
            
            // Raw output
            foreach ($list as $row) {
                echo $row['first_name']; // where `first_name` as filed name
                echo $row['last_name']; // where `last_name` as filed name
            }
            
            // With GridView and ArrayDataProvider
            $dataProvider = new \yii\data\ArrayDataProvider([
                'allModels' => $list
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

# Status and version [ready to use]
* v.1.1.1 - Update README.md
* v.1.1.0 - Multi-language support
* v.1.0.4 - Change namespace of DynamicModel
* v.1.0.3 - Log activity
* v.1.0.2 - Added pagination, up to date dependencies
* v.1.0.1 - CRUD for blocks, content and fields
* v.1.0.0 - Added content component and preview of content blocks/lists