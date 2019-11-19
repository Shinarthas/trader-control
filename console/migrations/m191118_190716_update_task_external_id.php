<?php

use yii\db\Migration;

/**
 * Class m191118_190716_update_task_external_id
 */
class m191118_190716_update_task_external_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('task','external_id',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('task','external_id',$this->integer());
        echo "m191118_190716_update_task_external_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191118_190716_update_task_external_id cannot be reverted.\n";

        return false;
    }
    */
}
