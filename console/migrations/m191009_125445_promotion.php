<?php

use yii\db\Migration;

/**
 * Class m191009_125445_promotion
 */
class m191009_125445_promotion extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = Yii::$app->db->tablePrefix . 'promotion';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'market_id' => $this->integer()->notNull(),
			'enabled' => $this->integer()->notNull(),
			'mode' => $this->integer()->notNull(),
			'currency_one' => $this->integer()->notNull(),
			'currency_two' => $this->integer()->notNull(),
			'settings_json' => $this->text()->notNull(),
			'started_at' =>  $this->integer()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_125445_promotion cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_125445_promotion cannot be reverted.\n";

        return false;
    }
    */
}
