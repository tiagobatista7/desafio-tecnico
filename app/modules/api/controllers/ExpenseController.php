<?php

namespace app\modules\api\controllers;

use app\modules\api\models\ExpenseCreateForm;
use app\modules\api\models\ExpenseUpdateForm;
use Yii;
use yii\rest\ActiveController;
use yii\filters\auth\CompositeAuth;
use app\components\JwtAuth;
use app\modules\api\services\ExpenseService;

class ExpenseController extends ActiveController
{
    public $modelClass = 'app\models\Expense';

    private ExpenseService $expenseService;

    public function __construct($id, $module, $config = [])
    {
        $this->expenseService = new ExpenseService();
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                JwtAuth::class,
            ],
            'except' => ['options'],
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
        return $actions;
    }

    public function actionIndex($category = null, $month = null, $year = null)
    {
        try {
            return $this->expenseService->list(Yii::$app->user->id, $category, $month, $year);
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Erro ao listar despesas.'];
        }
    }

    public function actionView($id)
    {
        try {
            return $this->expenseService->view($id, Yii::$app->user->id);
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->response->statusCode = $e instanceof \yii\web\NotFoundHttpException ? 404 : 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        try {
            $form = new ExpenseCreateForm();
            $form->load(Yii::$app->request->getBodyParams(), '');
            $result = $this->expenseService->create($form, Yii::$app->user->id);

            if (isset($result['errors'])) {
                return $result;
            }

            Yii::$app->response->statusCode = 201;
            return $result;
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Erro ao criar despesa.'];
        }
    }

    public function actionUpdate($id)
    {
        try {
            $form = new ExpenseUpdateForm();
            $form->load(Yii::$app->request->getBodyParams(), '');
            return $this->expenseService->update($id, $form, Yii::$app->user->id);
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->response->statusCode = $e instanceof \yii\web\NotFoundHttpException ? 404 : 500;
            return ['error' => $e->getMessage()];
        }
    }

    public function actionDelete($id)
    {
        try {
            $success = $this->expenseService->delete($id, Yii::$app->user->id);
            if (!$success) {
                Yii::$app->response->statusCode = 400;
                return ['error' => 'Não foi possível deletar a despesa'];
            }

            Yii::$app->response->statusCode = 204;
            return null;
        } catch (\Throwable $e) {
            Yii::error($e, __METHOD__);
            Yii::$app->response->statusCode = $e instanceof \yii\web\NotFoundHttpException ? 404 : 500;
            return ['error' => $e->getMessage()];
        }
    }
}
