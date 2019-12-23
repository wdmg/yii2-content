<?php

namespace wdmg\content\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use wdmg\content\models\Blocks;
use wdmg\content\models\BlocksSearch;

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

    /**
     * Finds the Newsletters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ActiveRecord model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Blocks::findOne($id)) !== null)
            return $model;

        throw new NotFoundHttpException(Yii::t('app/modules/content', 'The requested page does not exist.'));
    }
}