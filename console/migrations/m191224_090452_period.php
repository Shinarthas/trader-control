<?php

use yii\db\Migration;

/**
 * Class m191224_090452_period
 */
class m191224_090452_period extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('demo_balance','period',$this->integer());
        $this->addColumn('demo_task','period',$this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('demo_balance','period');
        $this->dropColumn('demo_task','period');
        echo "m191224_090452_period cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191224_090452_period cannot be reverted.\n";

        return false;
    }
    */
}
