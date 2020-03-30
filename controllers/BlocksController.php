<?php

namespace wdmg\content\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use wdmg\content\models\Blocks;
use wdmg\content\models\BlocksSearch;
use yii\web\NotFoundHttpException;

/**
 * BlocksController implements the CRUD actions.
 */
class BlocksController extends Controller
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


    /**
     * Lists of all Content blocks.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BlocksSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, ['type' => Blocks::CONTENT_BLOCK_TYPE_ONCE]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module
        ]);
    }


    public function actionView($id)
    {
        $model = self::findModel($id);
        $rows = $model->getBlockContent($model->id, true);
        $dataProvider = new \yii\data\ArrayDataProvider([
            'allModels' => $rows,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->renderAjax('_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'module' => $this->module
        ]);
    }

    public function actionCreate()
    {
        $model = new Blocks();
        $model->type = $model::CONTENT_BLOCK_TYPE_ONCE;
        $model->fields = "";

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'alias' => $model->alias, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'New content block `' . $model->title . '` with ID `' . $model->id . '` has been successfully added.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/content', 'Content block has been successfully added!')
                    );
                    return $this->redirect(['blocks/index']);
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while add the content block: ' . $model->title,
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/content', 'An error occurred while add the content block.')
                    );
                }
            }
        }

        return $this->render('create', [
            'module' => $this->module,
            'model' => $model
        ]);
    }

    public function actionUpdate($id)
    {
        $model = self::findModel($id);
        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'alias' => $model->alias, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->save()) {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'Content block `' . $model->title . '` with ID `' . $model->id . '` has been successfully updated.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/content',
                            'OK! Content block `{title}` successfully updated.',
                            [
                                'title' => $model->title
                            ]
                        )
                    );
                    return $this->redirect(['blocks/index']);
                } else {
                    // Log activity
                    if (
                        class_exists('\wdmg\activity\models\Activity') &&
                        $this->module->moduleLoaded('activity') &&
                        isset(Yii::$app->activity)
                    ) {
                        Yii::$app->activity->set(
                            'An error occurred while update the content block `' . $model->title . '` with ID `' . $model->id . '`.',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );
                    }

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/content',
                            'An error occurred while update a content block `{title}`.',
                            [
                                'title' => $model->title
                            ]
                        )
                    );
                }
            }
        }

        return $this->render('update', [
            'module' => $this->module,
            'model' => $model
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            // Log activity
            if (
                class_exists('\wdmg\activity\models\Activity') &&
                $this->module->moduleLoaded('activity') &&
                isset(Yii::$app->activity)
            ) {
                Yii::$app->activity->set(
                    'Content block `' . $model->title . '` with ID `' . $model->id . '` has been successfully deleted.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );
            }

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/content',
                    'OK! Content block `{title}` successfully deleted.',
                    [
                        'title' => $model->title
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
                    'An error occurred while deleting the content block `' . $model->title . '` with ID `' . $model->id . '`.',
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );
            }

            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/content',
                    'An error occurred while deleting a content block `{title}`.',
                    [
                        'title' => $model->title
                    ]
                )
            );
        }
        return $this->redirect(['blocks/index']);
    }

    /**
     * Finds the Newsletters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActiveRecord model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Blocks::findOne(['id' => $id, 'type' => Blocks::CONTENT_BLOCK_TYPE_ONCE])) !== null)
            return $model;

        throw new NotFoundHttpException(Yii::t('app/modules/content', 'The requested block does not exist.'));
    }
}