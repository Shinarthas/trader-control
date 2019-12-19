<?php

use yii\db\Migration;

/**
 * Class m191218_091936_copany
 */
class m191218_091936_copany extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('company',[
            'id'=>$this->primaryKey(),
            'settings'=>$this->json(),
            'trigger_score'=>$this->float(),
            'maximal_stake'=>$this->float(),
            'strategy'=>$this->json(),
            'timeout'=>$this->integer(),
            'created_at'=>$this->timestamp()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_091936_copany cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_091936_copany cannot be reverted.\n";

        return false;
    }
    */
}
