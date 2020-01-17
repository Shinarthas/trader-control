<?php

use yii\db\Migration;

/**
 * Class m200116_103142_task_update2
 */
class m200116_103142_task_update2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('task','currency_one',$this->string());
        $this->addColumn('task','currency_two',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200116_103142_task_update2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200116_103142_task_update2 cannot be reverted.\n";

        return false;
    }
    */
}
