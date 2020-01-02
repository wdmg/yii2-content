<?php

namespace wdmg\content\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%content_fields}}".
 *
 * @property int $id
 * @property string $label
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
            'sluggable' =>  [
                'class' => SluggableBehavior::class,
                'attribute' => ['label'],
                'slugAttribute' => 'name',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
                'immutable' => true,
                'value' => function ($event) {
                    return Inflector::slug(mb_substr($this->label, 0, 45), '_');
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
            [['block_id', 'label', 'name', 'type'], 'required'],
            [['label', 'name'], 'string', 'min' => 3, 'max' => 45],
            [['block_id', 'type', 'sort_order'], 'integer'],
            ['params', 'string'],
            ['name', 'match', 'pattern' => '/^[A-Za-z0-9\_]+$/', 'message' => Yii::t('app/modules/content','It allowed only Latin alphabet, numbers and «_» character.')],
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
            'block_id' => Yii::t('app/modules/content', 'Block ID'),
            'label' => Yii::t('app/modules/content', 'Label'),
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

    /**
     * @return array of list
     */
    public function getTypesList($allStatuses = false)
    {
        if ($allStatuses)
            $list[] = [
                '*' => Yii::t('app/modules/content', 'All statuses')
            ];

        $list[] = [
            self::CONTENT_FIELD_TYPE_STRING => Yii::t('app/modules/content', 'String'),
            self::CONTENT_FIELD_TYPE_TEXT => Yii::t('app/modules/content', 'Text'),
            self::CONTENT_FIELD_TYPE_HTML => Yii::t('app/modules/content', 'HTML')

        ];

        return $list;
    }
}