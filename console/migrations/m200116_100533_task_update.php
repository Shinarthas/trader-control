<?php

use yii\db\Migration;

/**
 * Class m200116_100533_task_update
 */
class m200116_100533_task_update extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task','start_rate',$this->decimal(50,10));
        $this->addColumn('task','group_id',$this->integer());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200116_100533_task_update cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200116_100533_task_update cannot be reverted.\n";

        return false;
    }
    */
}
