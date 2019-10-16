<?php

use yii\db\Migration;

/**
 * Class m191009_124812_balance
 */
class m191009_124812_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'account_balance';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'account_id' => $this->integer()->notNull(),
            'currency_id' => $this->integer()->notNull(),
			'market_id' => $this->integer()->notNull(),
			'type' => $this->integer()->notNull(),
			'value' => $this->float()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_124812_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_124812_balance cannot be reverted.\n";

        return false;
    }
    */
}
