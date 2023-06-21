<?php

namespace wdmg\content\components;


/**
 * Yii2 Content component
 *
 * @category        Content
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-content
 * @copyright       Copyright (c) 2019 - 2023 W.D.M.Group, Ukraine
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
     * Returns a block or list of content by ID or alias.
     *
     * @param null $id
     * @param null $locale
     * @param bool $rawContent
     * @return array|mixed|null
     */
    public function get($id = null, $locale = null, $rawContent = false)
    {
        if (is_null($id))
            return null;

        if (is_null($locale))
            $locale = Yii::$app->language;

        if ($model = Blocks::findModel($id)) {
            if ($model->type == Blocks::CONTENT_BLOCK_TYPE_LIST) {
                $rows = $model->getListContent($model->id, $locale, true);
                if (!$rawContent) {
                    $data = ArrayHelper::map($rows, 'name', 'content', 'row_order');
                    return array_values($data);
                }
                return $rows;
            } else if ($model->type == Blocks::CONTENT_BLOCK_TYPE_ONCE) {
                $rows = $model->getBlockContent($model->id, $locale, true);
                if (!$rawContent) {
                    $data = ArrayHelper::map($rows, 'name', 'content', 'field_order');
                    return array_reduce($data, 'array_merge', []);
                }
                return $rows;
            }
        }
        return null;
    }
}