<?php

namespace wdmg\content\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "{{%content}}".
 *
 * @property int $id
 * @property int $field_id
 * @property int $block_id
 * @property string $content
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Content extends ActiveRecord
{

    public $block;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%content}}';
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
            [['field_id', 'block_id', 'content'], 'required'],
            [['field_id', 'block_id'], 'integer'],
            ['content', 'string'],
            [['created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users') && (Yii::$app->hasModule('admin/users') || Yii::$app->hasModule('users'))) {
            $rules[] = [['created_by', 'updated_by'], 'safe'];
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
            'field' => Yii::t('app/modules/content', 'Field'),
            'field_id' => Yii::t('app/modules/content', 'Field ID'),
            'block' => Yii::t('app/modules/content', 'Block'),
            'block_id' => Yii::t('app/modules/content', 'Block ID'),
            'content' => Yii::t('app/modules/content', 'Content'),
            'created_at' => Yii::t('app/modules/content', 'Created at'),
            'created_by' => Yii::t('app/modules/content', 'Created by'),
            'updated_at' => Yii::t('app/modules/content', 'Updated at'),
            'updated_by' => Yii::t('app/modules/content', 'Updated by'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Fields::class, ['id' => 'field_id']);
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return $this->created_by;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return $this->updated_by;
    }
}