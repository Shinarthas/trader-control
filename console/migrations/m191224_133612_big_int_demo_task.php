<?php

use yii\db\Migration;

/**
 * Class m191224_133612_big_int_demo_task
 */
class m191224_133612_big_int_demo_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('demo_task','tokens_count',$this->decimal(50,6)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191224_133612_big_int_demo_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191224_133612_big_int_demo_task cannot be reverted.\n";

        return false;
    }
    */
}
