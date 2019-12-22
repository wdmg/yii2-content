<?php

namespace wdmg\content\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%content_fields}}".
 *
 * @property int $id
 * @property string $name
 * @property int $type
 * @property int $sort_order
 * @property string $params
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Fields extends ActiveRecord
{

    const CONTENT_FIELD_TYPE_STRING = 1;
    const CONTENT_FIELD_TYPE_TEXT = 2;
    const CONTENT_FIELD_TYPE_HTML = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%content_fields}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' =>  [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ]
        ];

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'type', 'content'], 'required'],
            ['name', 'string', 'min' => 3, 'max' => 45],
            [['type', 'sort_order'], 'integer'],
            ['params', 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users') && isset(Yii::$app->modules['users'])) {
            $rules[] = [['created_by', 'updated_by'], 'required'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/content', 'ID'),
            'name' => Yii::t('app/modules/content', 'Name'),
            'type' => Yii::t('app/modules/content', 'Type'),
            'sort_order' => Yii::t('app/modules/content', 'Sort order'),
            'params' => Yii::t('app/modules/content', 'Params'),
            'created_at' => Yii::t('app/modules/content', 'Created at'),
            'created_by' => Yii::t('app/modules/content', 'Created by'),
            'updated_at' => Yii::t('app/modules/content', 'Updated at'),
            'updated_by' => Yii::t('app/modules/content', 'Updated by'),
        ];
    }
}