<?php

use yii\db\Migration;

class m250601_045557_alter_expense_table extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('{{%expense}}', 'value', $this->decimal(12, 2)->notNull());
    }

    public function safeDown()
    {
        $this->alterColumn('{{%expense}}', 'value', $this->decimal(10, 2)->notNull());
    }
}
