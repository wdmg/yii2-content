<?php

namespace wdmg\content\controllers;

use wdmg\content\models\Items;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;
use wdmg\content\models\Blocks;
use wdmg\content\models\Fields;
use wdmg\content\models\Content;

/**
 * ContentController implements the CRUD actions.
 */
class ContentController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST', 'GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    public function actionIndex($block_id)
    {
        $items = null;
        $columns = [];
        $model = new Content();
        $block = Blocks::findModel(intval($block_id));

        // Preparing dynamic columns for GridView
        $fields = ArrayHelper::map($block->getFields(), 'name', 'label', 'sort_order');
        foreach (array_values($fields) as $field) {
            foreach ($field as $attribute => $label) {
                $columns[$attribute] = [
                    'attribute' => $attribute,
                    'label' => $label
                ];
            }
        }

        // Make a selection of content, depending on the type (block or list), the selection logic changes
        if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
            $rows = $block->getListContent($block->id, true);
            $data = ArrayHelper::map($rows, 'name', 'content', 'row_order');
            $items = array_keys($data);
            $data = array_values($data);
            $dataProvider = new ArrayDataProvider([
                'allModels' => $data,
                'sort' => [
                    'attributes' => $columns
                ],
                'pagination' => [
                    'pageSize' => 20,
                ],
            ]);
        } else {
            $query = $model::find()->where(['block_id' => intval($block_id)]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        return $this->render('index', [
            'model' => $model,
            'items' => $items,
            'block' => $block,
            'columns' => $columns,
            'dataProvider' => $dataProvider,
            'module' => $this->module
        ]);
    }

    public function actionCreate($block_id)
    {
        $block = Blocks::findModel(intval($block_id));
        $fields = Fields::find()->where(['block_id' => $block_id])->orderBy('sort_order')->asArray()->all();
        $attributes = ArrayHelper::getColumn($fields, 'name');
        $model = new \wdmg\base\DynamicModel($attributes);

        // Add validation rules according to field types
        foreach ($fields as $field) {
            if ($name = $field['name']) {

                if ($label = $field['label'])
                    $model->setAttributeLabel([$name => $label]);

                if ($type = $field['type']) {
                    if ($type == "string")
                        $model->addRule([$name], 'string', ['max' => 255]);
                    elseif ($type == "integer")
                        $model->addRule([$name], 'integer');
                    elseif ($type == "email")
                        $model->addRule([$name], 'email');
                    else
                        $model->addRule([$name], 'string');
                }

                if (isset($field['params']['required']))
                    $model->addRule([$name], 'required');

            }
        }

        // Load and check editable content
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $hasError = false;
            $block_id = intval($block->id);
            $attributes = $model->getAttributes();

            $row_order = 10;
            if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                $row_order = Items::find()->where(['block_id' => $block_id])->max('row_order');
                $row_order = intval($row_order) + 10;
            }

            foreach ($fields as $field) {
                if ($name = $field['name']) {
                    if ($block_id && isset($field['id']) && isset($attributes[$name])) {

                        $field_id = intval($field['id']);
                        $content = new Content();
                        $content->block_id = $block_id;
                        $content->field_id = $field_id;
                        $content->content = $attributes[$name];

                        // Validate the content model
                        if ($content->validate()) {

                            if (!$content->save())
                                $hasError = true;

                            if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {

                                if (!$items = Items::find()->where(['block_id' => $block_id, 'ext_id' => $content->id])->one())
                                    $items = new Items();

                                $items->block_id = $block_id;
                                $items->ext_id = $content->id;

                                if (!$items->id)
                                    $items->row_order = $row_order;
                                

                                if (!$items->save())
                                    $hasError = true;

                            }
                        }
                    }
                }
            }

            if (!$hasError) {
                if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'New row of content list with ID `' . $block->id . '` has been successfully added.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'A row has been successfully added!')
                    );
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'New content with ID `' . $block->id . '` has been successfully added.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'Content has been successfully added!')
                    );
                }
                return $this->redirect(['content/index', 'block_id' => $block_id]);
            } else {
                if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while add the row of content list, ID: ' . $block->id,
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while add the row.')
                    );
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while add the content, ID: ' . $block->id,
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while add the content.')
                    );
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'block' => $block,
            'module' => $this->module
        ]);
    }

    public function actionUpdate($block_id, $row_order = null)
    {
        $block = Blocks::findModel(intval($block_id));
        $fields = Fields::find()->where(['block_id' => $block_id])->orderBy('sort_order')->asArray()->all();
        $attributes = ArrayHelper::getColumn($fields, 'name');
        $model = new \wdmg\base\DynamicModel($attributes);

        // Add validation rules according to field types
        foreach ($fields as $field) {
            if ($name = $field['name']) {

                if ($label = $field['label'])
                    $model->setAttributeLabel([$name => $label]);

                if ($type = $field['type']) {
                    if ($type == "string")
                        $model->addRule([$name], 'string', ['max' => 255]);
                    elseif ($type == "integer")
                        $model->addRule([$name], 'integer');
                    elseif ($type == "email")
                        $model->addRule([$name], 'email');
                    else
                        $model->addRule([$name], 'string');
                }

                if (isset($field['params']['required']))
                    $model->addRule([$name], 'required');

            }
        }

        // Load existing content, if any
        if (!is_null($row_order) && $block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
            // Content list selection
            if ($items = Items::find()->where(['block_id' => intval($block_id), 'row_order' => intval($row_order)])->asArray()->all()) {
                $ext_id = ArrayHelper::getColumn($items, 'ext_id');
                // Selecting a position from the content list
                if ($contents = Content::find()->where(['id' => $ext_id, 'block_id' => intval($block_id)])->asArray()->all()) {
                    foreach ($contents as $content) {
                        foreach ($fields as $field) {
                            // If the attribute ID is already in the content table, load the value for the model property
                            if (isset($field['id']) && $content["field_id"]) {
                                if ($field['id'] == $content["field_id"])
                                    $model->setAttributes([$field['name'] => $content['content']]);
                            }
                        }
                    }
                }
            }
        } else {
            // Content block selection
            if ($contents = Content::find()->where(['block_id' => intval($block_id)])->asArray()->all()) {
                foreach ($contents as $content) {
                    foreach ($fields as $field) {
                        // If the attribute ID is already in the content table, load the value for the model property
                        if (isset($field['id']) && $content["field_id"]) {
                            if ($field['id'] == $content["field_id"])
                                $model->setAttributes([$field['name'] => $content['content']]);
                        }
                    }
                }
            }
        }

        // Load and check editable content
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $hasError = false;
            $block_id = intval($block->id);
            $attributes = $model->getAttributes();
            foreach ($fields as $field) {
                if ($name = $field['name']) {
                    if ($block_id && isset($field['id']) && isset($attributes[$name])) {

                        // Check if content already exists and should be updated
                        $field_id = intval($field['id']);

                        if (!$content = Content::find()->where(['block_id' => $block_id, 'field_id' => $field_id])->one())
                            $content = new Content();

                        $content->block_id = $block_id;
                        $content->field_id = $field_id;
                        $content->content = $attributes[$name];

                        // Производим валидацию модели контента
                        if ($content->validate()) {
                            if (!$content->save()) {
                                $hasError = true;
                            }
                        }
                    }
                }
            }

            if (!$hasError) {
                if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'Content row with list ID `' . $block->id . '` has been successfully updated.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'A row has been successfully updated!')
                    );
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'Content with block ID `' . $block->id . '` has been successfully updated.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'Content has been successfully updated!')
                    );
                }
                return $this->redirect(['content/index', 'block_id' => $block_id]);
            } else {
                if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while update the content row with list ID `' . $block->id . '`.',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while updating the row.')
                    );
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while update the content with block ID `' . $block->id . '`.',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while updating the content.')
                    );
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'block' => $block,
            'module' => $this->module
        ]);

    }

    public function actionDelete($block_id, $row_order = null)
    {
        $hasErrors = false;
        $block = Blocks::findModel(intval($block_id));

        if (!is_null($row_order) && $block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
            $items = Items::find()->where(['block_id' => $block->id, 'row_order' => intval($row_order)])->select('ext_id')->all();
            $ext_ids = ArrayHelper::toArray($items, 'ext_id');
            $ids = ArrayHelper::getColumn($ext_ids, 'ext_id');

            if (!Items::deleteAll(['block_id' => $block->id, 'row_order' => intval($row_order)]))
                $hasErrors = true;

            if (!Content::deleteAll(['id' => $ids, 'block_id' => $block->id]))
                $hasErrors = true;

        } else if (!Content::deleteAll(['block_id' => $block->id])) {
            $hasErrors = true;
        }

        if (!$hasErrors) {
            if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                // Log activity
                if (
                    class_exists('\wdmg\activity\models\Activity') &&
                    $this->module->moduleLoaded('activity') &&
                    isset(Yii::$app->activity)
                ) {
                    Yii::$app->activity->set(
                        'Row from list `' . $block->title . '` with ID `' . $block->id . '` has been successfully deleted.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );
                }

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(
                        'app/modules/content',
                        'OK! Row from list `{title}` successfully deleted.',
                        [
                            'title' => $block->title
                        ]
                    )
                );
            } else {
                // Log activity
                if (
                    class_exists('\wdmg\activity\models\Activity') &&
                    $this->module->moduleLoaded('activity') &&
                    isset(Yii::$app->activity)
                ) {
                    Yii::$app->activity->set(
                        'Content for `' . $block->title . '` with ID `' . $block->id . '` has been successfully deleted.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );
                }

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(
                        'app/modules/content',
                        'OK! Content for `{title}` successfully deleted.',
                        [
                            'title' => $block->title
                        ]
                    )
                );
            }
        } else {
            if ($block::CONTENT_BLOCK_TYPE_LIST == $block->type) {
                // Log activity
                if (
                    class_exists('\wdmg\activity\models\Activity') &&
                    $this->module->moduleLoaded('activity') &&
                    isset(Yii::$app->activity)
                ) {
                    Yii::$app->activity->set(
                        'An error occurred while deleting the row of list `' . $block->title . '` with ID `' . $block->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );
                }

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/content',
                        'An error occurred while deleting a row of list `{title}`.',
                        [
                            'title' => $block->title
                        ]
                    )
                );
            } else {
                // Log activity
                if (
                    class_exists('\wdmg\activity\models\Activity') &&
                    $this->module->moduleLoaded('activity') &&
                    isset(Yii::$app->activity)
                ) {
                    Yii::$app->activity->set(
                        'An error occurred while deleting the content for `' . $block->title . '` with ID `' . $block->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );
                }

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/content',
                        'An error occurred while deleting a content for `{title}`.',
                        [
                            'title' => $block->title
                        ]
                    )
                );
            }
        }
        return $this->redirect(['content/index', 'block_id' => $block_id]);
    }

    /**
     * Finds the Newsletters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @param integer $block_id
     * @return ActiveRecord model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id, $block_id = null)
    {
        if (($model = Content::findOne(['id' => $id, 'block_id' => $block_id])) !== null)
            return $model;

        throw new NotFoundHttpException(Yii::t('app/modules/content', 'The requested filed does not exist.'));
    }
}