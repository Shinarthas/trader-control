<?php

use yii\db\Migration;

/**
 * Class m191216_090144_update_task
 */
class m191216_090144_update_task extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('task','value',$this->float());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('task','value',$this->integer());
        echo "m191216_090144_update_task cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191216_090144_update_task cannot be reverted.\n";

        return false;
    }
    */
}
