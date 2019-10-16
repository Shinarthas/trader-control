<?php

use yii\db\Migration;

/**
 * Class m191009_125704_promotion_account
 */
class m191009_125704_promotion_account extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableName = Yii::$app->db->tablePrefix . 'promotion_account';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'promotion_id' => $this->integer()->notNull(),
            'account_id' => $this->integer()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_125704_promotion_account cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_125704_promotion_account cannot be reverted.\n";

        return false;
    }
    */
}
