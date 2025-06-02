<?php

namespace app\modules\api\services;

use app\modules\api\models\Expense;
use app\modules\api\models\ExpenseCreateForm;
use app\modules\api\models\ExpenseUpdateForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;

class ExpenseService
{
    public function getList(int $userId, ?string $category = null, ?int $month = null, ?int $year = null): ActiveDataProvider
    {
        $query = Expense::find()->where(['user_id' => $userId]);

        if ($category !== null) {
            $query->andWhere(['category' => $category]);
        }

        if ($month !== null && $year !== null) {
            $query->andWhere([
                'MONTH(expense_date)' => $month,
                'YEAR(expense_date)' => $year,
            ]);
        }

        $query->orderBy(['expense_date' => SORT_DESC]);

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 20],
        ]);
    }

    public function getOne(int $id, int $userId): Expense
    {
        $expense = Expense::findOne(['id' => $id, 'user_id' => $userId]);

        if (!$expense) {
            throw new NotFoundHttpException('Despesa não encontrada.');
        }

        return $expense;
    }

    public function create(ExpenseCreateForm $form, int $userId): array|Expense
    {
        if (!$form->validate()) {
            return ['success' => false, 'errors' => $form->errors];
        }

        $expense = new Expense([
            'user_id' => $userId,
            'description' => $form->description,
            'category' => $form->category,
            'value' => $form->value,
            'expense_date' => $form->expense_date,
        ]);

        if ($expense->save()) {
            return $expense;
        }

        return ['success' => false, 'errors' => $expense->errors];
    }

    public function update(int $id, ExpenseUpdateForm $form, int $userId): array|Expense
    {
        $expense = Expense::findOne(['id' => $id, 'user_id' => $userId]);

        if (!$expense) {
            throw new NotFoundHttpException('Despesa não encontrada.');
        }

        if (!$form->validate()) {
            return ['success' => false, 'errors' => $form->errors];
        }

        $expense->setAttributes([
            'description' => $form->description,
            'category' => $form->category,
            'value' => $form->value,
            'expense_date' => $form->expense_date,
        ]);

        if ($expense->save()) {
            return $expense;
        }

        return ['success' => false, 'errors' => $expense->errors];
    }

    public function delete(int $id, int $userId): array
    {
        $expense = Expense::findOne(['id' => $id, 'user_id' => $userId]);

        if (!$expense) {
            throw new NotFoundHttpException('Despesa não encontrada.');
        }

        if ($expense->delete()) {
            return ['success' => true];
        }

        return ['success' => false, 'errors' => $expense->errors];
    }
}
