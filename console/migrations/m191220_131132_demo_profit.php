<?php

use yii\db\Migration;

/**
 * Class m191220_131132_demo_profit
 */
class m191220_131132_demo_profit extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('demo_profit',[
           'id'=>$this->primaryKey(),
           'value'=> $this->float(),
            'timestamp'=>$this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191220_131132_demo_profit cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191220_131132_demo_profit cannot be reverted.\n";

        return false;
    }
    */
}
