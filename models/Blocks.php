<?php

namespace wdmg\content\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use wdmg\content\models\Fields;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%content_blocks}}".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $alias
 * @property string $fields
 * @property int $type
 * @property int $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Blocks extends ActiveRecord
{
    const CONTENT_BLOCK_TYPE_ONCE = 1;
    const CONTENT_BLOCK_TYPE_LIST = 2;

    const CONTENT_BLOCK_STATUS_DRAFT = 0;
    const CONTENT_BLOCK_STATUS_PUBLISHED = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%content_blocks}}';
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
            'sluggable' =>  [
                'class' => SluggableBehavior::class,
                'attribute' => ['title'],
                'slugAttribute' => 'alias',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
                'immutable' => true,
                'value' => function ($event) {
                    return mb_substr($this->title, 0, 64);
                }
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
            [['title', 'alias', 'type'], 'required'],
            //[['title', 'alias', 'fields', 'type'], 'required'],
            [['title', 'alias'], 'string', 'min' => 3, 'max' => 64],
            ['description', 'string', 'max' => 255],
            ['fields', 'string'],
            [['type'], 'integer'],
            [['status'], 'boolean'],
            ['alias', 'unique', 'message' => Yii::t('app/modules/content', 'Alias attribute must be unique.')],
            ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => Yii::t('app/modules/pages','It allowed only Latin alphabet, numbers and the «-», «_» characters.')],
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
            'title' => Yii::t('app/modules/content', 'Title'),
            'description' => Yii::t('app/modules/content', 'Description'),
            'alias' => Yii::t('app/modules/content', 'Alias'),
            'fields' => Yii::t('app/modules/content', 'Fields'),
            'type' => Yii::t('app/modules/content', 'Type'),
            'status' => Yii::t('app/modules/content', 'Status'),
            'created_at' => Yii::t('app/modules/content', 'Created at'),
            'created_by' => Yii::t('app/modules/content', 'Created by'),
            'updated_at' => Yii::t('app/modules/content', 'Updated at'),
            'updated_by' => Yii::t('app/modules/content', 'Updated by'),
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {

        if (is_array($this->fields))
            $this->fields = Json::encode($this->fields);

        parent::beforeSave($insert);
    }

    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {

        if (!($this->fields = Json::decode($this->fields)))
            $this->fields = [];

        parent::afterFind();
    }

    /**
     * @return array or null
     */
    public function getFields($fields = null)
    {

        if (is_array($fields)) {
            return Fields::find()->where(['id' => $fields])->asArray()->all();
        } else {
            return null;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreated()
    {
        if (class_exists('\wdmg\users\models\Users') && (Yii::$app->hasModule('admin/users') || Yii::$app->hasModule('users')))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdated()
    {
        if (class_exists('\wdmg\users\models\Users') && (Yii::$app->hasModule('admin/users') || Yii::$app->hasModule('users')))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return null;
    }
}