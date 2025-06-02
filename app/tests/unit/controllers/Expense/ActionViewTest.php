<?php

namespace unit\controllers\Expense;

use app\modules\api\controllers\ExpenseController;
use app\modules\api\services\ExpenseService;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;
use Yii;
use Codeception\Test\Unit;

class ActionViewTest extends Unit
{
    private ExpenseController $controller;

    protected function _before()
    {
        $this->controller = new ExpenseController('expense', Yii::$app->getModule('api'));

        Yii::$app->set('user', new class extends User {
            public $identityClass = \app\models\User::class;

            public function getId()
            {
                return 1;
            }

            public function getIsGuest()
            {
                return false;
            }
        });

        Yii::$app->set('request', new class extends Request {});
        Yii::$app->set('response', new Response());
    }

    public function testActionViewSuccess()
    {
        $mockService = $this->make(ExpenseService::class, [
            'view' => function ($id, $userId) {
                return ['id' => $id, 'title' => 'Despesa Visualizada', 'userId' => $userId];
            }
        ]);

        $this->injectService($mockService);

        $result = $this->controller->actionView(1);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('Despesa Visualizada', $result['title']);
        $this->assertEquals(1, $result['userId']);
        $this->assertEquals(200, Yii::$app->response->statusCode);
    }

    public function testActionViewNotFound()
    {
        $mockService = $this->make(ExpenseService::class, [
            'view' => function ($id, $userId) {
                throw new \yii\web\NotFoundHttpException('Despesa não encontrada');
            }
        ]);

        $this->injectService($mockService);

        $result = $this->controller->actionView(999);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Despesa não encontrada', $result['error']);
        $this->assertEquals(404, Yii::$app->response->statusCode);
    }

    public function testActionViewError()
    {
        $mockService = $this->make(ExpenseService::class, [
            'view' => function ($id, $userId) {
                throw new \Exception('Erro inesperado');
            }
        ]);

        $this->injectService($mockService);

        $result = $this->controller->actionView(1);

        $this->assertArrayHasKey('error', $result);
        $this->assertEquals('Erro inesperado', $result['error']);
        $this->assertEquals(500, Yii::$app->response->statusCode);
    }

    private function injectService($mockService): void
    {
        $ref = new \ReflectionClass($this->controller);
        $prop = $ref->getProperty('expenseService');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $mockService);
    }
}
