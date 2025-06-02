<?php

namespace app\modules\api\models;

use app\models\User;
use Yii;
use yii\db\ActiveRecord;

class Expense extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%expense}}';
    }

    public function rules()
    {
        return [
            [['description', 'category', 'value', 'expense_date'], 'required'],
            [['description'], 'string'],
            [['category'], 'in', 'range' => ['alimentação', 'transporte', 'lazer']],
            [['value'], 'number'],
            [['expense_date'], 'date', 'format' => 'php:Y-m-d'],
            [['user_id'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'description' => 'Descrição',
            'category' => 'Categoria',
            'value' => 'Valor',
            'expense_date' => 'Data da Despesa',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
