<?php

use yii\db\Migration;

/**
 * Class m191205_093507_balance_check
 */
class m191205_093507_balance_check extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('account', 'check_balance',$this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('account','check_balance');
        echo "m191205_093507_balance_check cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191205_093507_balance_check cannot be reverted.\n";

        return false;
    }
    */
}
