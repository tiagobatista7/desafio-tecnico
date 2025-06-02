<?php

namespace unit\controllers\Expense;

use app\modules\api\controllers\ExpenseController;
use app\modules\api\models\Expense;
use app\modules\api\services\ExpenseService;
use yii\web\Response;
use yii\web\User;
use Yii;
use Codeception\Test\Unit;

class ActionDeleteTest extends Unit
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

        Yii::$app->set('response', new Response());
    }

    public function testActionDeleteSuccess()
    {
        $expense = new Expense([
            'user_id' => Yii::$app->user->id,
            'description' => 'Teste',
            'category' => 'Teste',
            'value' => 100,
            'expense_date' => date('Y-m-d'),
        ]);

        $this->assertTrue($expense->save());

        $controller = new ExpenseController('expense', Yii::$app);
        $controller->expenseService = new ExpenseService();

        $result = $controller->actionDelete($expense->id);

        $this->assertNull($result);
        $this->assertEquals(204, Yii::$app->response->statusCode);

        $this->assertNull(Expense::findOne($expense->id));
    }

    public function testActionDeleteNotFound()
    {
        $mockService = $this->make(ExpenseService::class, [
            'delete' => function () {
                throw new \yii\web\NotFoundHttpException('Despesa não encontrada');
            }
        ]);

        $this->injectService($mockService);
        $result = $this->controller->actionDelete(999);

        $this->assertEquals(404, Yii::$app->response->statusCode);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Despesa não encontrada', $result['error']);
    }

    public function testActionDeleteServerError()
    {
        $mockService = $this->make(ExpenseService::class, [
            'delete' => function () {
                throw new \Exception('Erro inesperado');
            }
        ]);

        $this->injectService($mockService);
        $result = $this->controller->actionDelete(1);

        $this->assertEquals(500, Yii::$app->response->statusCode);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringContainsString('Erro inesperado', $result['error']);
    }

    private function injectService($mockService): void
    {
        $ref = new \ReflectionClass($this->controller);
        $prop = $ref->getProperty('expenseService');
        $prop->setAccessible(true);
        $prop->setValue($this->controller, $mockService);
    }
}
