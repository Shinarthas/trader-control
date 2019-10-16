<?php

use yii\db\Migration;

/**
 * Class m191016_125339_currency_price
 */
class m191016_125339_currency_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = Yii::$app->db->tablePrefix . 'currency_price';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'currency_one' => $this->integer()->notNull(),
            'currency_two' => $this->integer()->notNull(),
			'market_id' => $this->integer()->notNull(),
			'buy_price' => $this->decimal(15,6)->notNull(),
			'sell_price' => $this->decimal(15,6)->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191016_125339_currency_price cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191016_125339_currency_price cannot be reverted.\n";

        return false;
    }
    */
}
