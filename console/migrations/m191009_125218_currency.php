<?php

use yii\db\Migration;

/**
 * Class m191009_125218_currency
 */
class m191009_125218_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = Yii::$app->db->tablePrefix . 'currency';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'symbol' => $this->string()->notNull(),
            'decimals' => $this->integer()->notNull(),
			'type' => $this->integer()->notNull(),
			'address' => $this->string()->notNull(),
			'data_json' => $this->text()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_125218_currency cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_125218_currency cannot be reverted.\n";

        return false;
    }
    */
}
