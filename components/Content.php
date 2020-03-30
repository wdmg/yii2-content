<?php

namespace wdmg\content\components;


/**
 * Yii2 Content component
 *
 * @category        Content
 * @version         1.0.3
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-content
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use wdmg\content\models\Blocks;

class Content extends Component
{

    /**
     *
     */
    public function get($id = null)
    {
        if (is_null($id))
            return null;

        if ($model = Blocks::findModel($id)) {
            if ($model->type == Blocks::CONTENT_BLOCK_TYPE_LIST) {
                $rows = $model->getListContent($model->id, true);
                $data = ArrayHelper::map($rows, 'name', 'content', 'row_order');
                return array_values($data);
            } else if ($model->type == Blocks::CONTENT_BLOCK_TYPE_ONCE) {
                $rows = $model->getBlockContent($model->id, true);
                $data = ArrayHelper::map($rows, 'name', 'content', 'field_order');
                return array_reduce($data, 'array_merge', []);
            }
        }
        return null;
    }
}