<?php

namespace unit\controllers\Expense;

use app\modules\api\controllers\ExpenseController;
use app\modules\api\services\ExpenseService;
use yii\web\Request;
use yii\web\Response;
use yii\web\User;
use Yii;
use Codeception\Test\Unit;

class ActionCreateTest extends Unit
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

        Yii::$app->set('request', new class extends Request {
            public function getBodyParams()
            {
                return ['title' => 'Nova Despesa', 'amount' => 250];
            }
        });

        Yii::$app->set('response', new Response());
    }

    public function testActionCreate()
    {
        $mockService = $this->make(ExpenseService::class, [
            'create' => fn() => ['id' => 1, 'title' => 'Nova Despesa']
        ]);

        $this->injectService($mockService);
        $result = $this->controller->actionCreate();

        $this->assertEquals(201, Yii::$app->response->statusCode);
        $this->assertEquals('Nova Despesa', $result['title']);
    }

    private function injectService($mockService): void
    {
        $ref = new \ReflectionClass($this->controller);
        $prop = $ref->getProperty('expenseService');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $mockService);
    }
}
