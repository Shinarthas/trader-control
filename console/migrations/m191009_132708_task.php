<?php

use yii\db\Migration;

/**
 * Class m191009_132708_task
 */
class m191009_132708_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = Yii::$app->db->tablePrefix . 'task';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'promotion_id' => $this->integer()->notNull(),
            'account_id' => $this->integer()->notNull(),
			'status' => $this->integer()->notNull(),
			'sell' => $this->integer()->notNull(),
			'value' => $this->integer()->notNull(),
			'random_curve' => $this->float()->notNull(),
			'tokens_count' => $this->decimal(15,6)->notNull(),
			'rate' => $this->decimal(15,6)->notNull(),
			'progress' => $this->integer()->notNull(),
			'data_json' => $this->text()->notNull(),
			'time' => $this->integer()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
			'loaded_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_132708_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_132708_task cannot be reverted.\n";

        return false;
    }
    */
}
