<?php

namespace wdmg\content\controllers;

use wdmg\content\models\Blocks;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use wdmg\content\models\Fields;

/**
 * FieldsController implements the CRUD actions.
 */
class FieldsController extends Controller
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
                    'delete' => ['POST'],
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
        $block = Blocks::findModel(intval($block_id));
        $model = new Fields();
        $query = $model::find()->where(['block_id' => intval($block_id)]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $this->render('index', [
            'model' => $model,
            'block' => $block,
            'dataProvider' => $dataProvider,
            'module' => $this->module
        ]);
    }

    public function actionCreate($block_id)
    {
        $model = new Fields();
        $block = Blocks::findModel(intval($block_id));
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {

                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'name' => $model->name, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {
                $sort_order = Fields::find()->where(['block_id' => $block->id])->max('sort_order');
                $model->block_id = $block->id;
                $model->sort_order = intval($sort_order) + 10;
                if ($model->save()) {
                    // Log activity
                    $this->module->logActivity(
                        'New content field `' . $model->label . '` with ID `' . $model->id . '` has been successfully added.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'Field has been successfully added!')
                    );
                    return $this->redirect(['fields/index', 'block_id' => $block->id]);
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while add the content field: ' . $model->label,
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while add the field.')
                    );
                }
            }
        }

        return $this->render('create', [
            'module' => $this->module,
            'model' => $model,
            'block' => $block
        ]);
    }

    public function actionUpdate($id, $block_id)
    {
        $block = Blocks::findModel(intval($block_id));
        $model = self::findModel($id, $block_id);
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'name' => $model->name, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    // Log activity
                    $this->module->logActivity(
                        'Field of content `' . $model->label . '` with ID `' . $model->id . '` has been successfully updated.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/content',
                            'OK! Field `{label}` successfully updated.',
                            [
                                'label' => $model->label
                            ]
                        )
                    );
                    return $this->redirect(['fields/index', 'block_id' => $block_id]);
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while update the content field `' . $model->label . '` with ID `' . $model->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/content',
                            'An error occurred while update a field `{label}`.',
                            [
                                'label' => $model->label
                            ]
                        )
                    );
                }
            }
        }

        return $this->render('update', [
            'module' => $this->module,
            'model' => $model,
            'block' => $block
        ]);
    }

    public function actionDelete($id, $block_id)
    {
        $model = $this->findModel(intval($id), intval($block_id));
        if ($model->delete()) {
            // Log activity
            $this->module->logActivity(
                'Field of content `' . $model->label . '` with ID `' . $model->id . '` has been successfully deleted.',
                $this->uniqueId . ":" . $this->action->id,
                'success',
                1
            );

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/content',
                    'OK! Field `{label}` successfully deleted.',
                    [
                        'label' => $model->label
                    ]
                )
            );
        } else {
            // Log activity
            $this->module->logActivity(
                'An error occurred while deleting the content field `' . $model->title . '` with ID `' . $model->id . '`.',
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );

            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/content',
                    'An error occurred while deleting a field `{label}`.',
                    [
                        'label' => $model->label
                    ]
                )
            );
        }
        return $this->redirect(['fields/index', 'block_id' => intval($block_id)]);
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
        if (($model = Fields::findOne(['id' => $id, 'block_id' => $block_id])) !== null)
            return $model;

        throw new NotFoundHttpException(Yii::t('app/modules/content', 'The requested filed does not exist.'));
    }
}