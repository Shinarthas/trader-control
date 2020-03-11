<?php

use yii\db\Migration;

/**
 * Class m200224_093100_user_order
 */
class m200224_093100_user_order extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task','is_user',$this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200224_093100_user_order cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200224_093100_user_order cannot be reverted.\n";

        return false;
    }
    */
}
