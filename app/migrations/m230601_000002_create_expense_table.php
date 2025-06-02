<?php

use yii\db\Migration;

class m230601_000002_create_expense_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%expense}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'description' => $this->text()->notNull(),
            'category' => "ENUM('alimentação','transporte','lazer') NOT NULL",
            'value' => $this->decimal(10, 2)->notNull(),
            'expense_date' => $this->date()->notNull(),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey('fk-expense-user', '{{%expense}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-expense-user', '{{%expense}}');
        $this->dropTable('{{%expense}}');
    }
}
