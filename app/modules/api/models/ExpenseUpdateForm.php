<?php

namespace app\modules\api\models;

use yii\base\Model;

class ExpenseUpdateForm extends Model
{
    public $description;
    public $category;
    public $value;
    public $expense_date;

    public function rules()
    {
        return [
            [['description', 'category', 'value', 'expense_date'], 'required', 'message' => '{attribute} é obrigatório.'],

            ['description', 'filter', 'filter' => 'trim'],
            ['category', 'filter', 'filter' => function ($value) {
                return mb_strtolower(trim($value));
            }],

            ['description', 'string', 'max' => 255, 'tooLong' => '{attribute} deve ter no máximo 255 caracteres.'],
            ['category', 'in', 'range' => ['alimentação', 'transporte', 'lazer'], 'message' => 'Categoria inválida.'],
            ['value', 'number', 'min' => 0.01, 'tooSmall' => 'O valor deve ser maior que zero.'],

            ['expense_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Data inválida, formato esperado: AAAA-MM-DD.'],
            ['expense_date', 'validateExpenseDate'],
        ];
    }

    public function validateExpenseDate($attribute, $params)
    {
        if (strtotime($this->$attribute) > time()) {
            $this->addError($attribute, 'A data da despesa não pode ser no futuro.');
        }
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Descrição',
            'category' => 'Categoria',
            'value' => 'Valor',
            'expense_date' => 'Data da despesa',
        ];
    }
}
