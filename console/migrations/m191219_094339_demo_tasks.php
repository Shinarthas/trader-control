<?php

use yii\db\Migration;

/**
 * Class m191219_094339_demo_tasks
 */
class m191219_094339_demo_tasks extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'demo_task';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer()->notNull(),
            'status' => $this->integer()->notNull(),
            'sell' => $this->integer()->notNull(),
            'tokens_count' => $this->decimal(15,6)->notNull(),
            'rate' => $this->decimal(15,6)->notNull(),
            'progress' => $this->integer()->notNull(),
            'data_json' => $this->text()->notNull(),
            'external_id' => $this->integer()->notNull(),
            'time' => $this->integer()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
            'loaded_at' =>  $this->integer()->notNull(),
            'currency_one' =>  $this->string(),
            'currency_two' =>  $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191219_094339_demo_tasks cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191219_094339_demo_tasks cannot be reverted.\n";

        return false;
    }
    */
}
