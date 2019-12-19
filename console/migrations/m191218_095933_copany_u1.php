<?php

use yii\db\Migration;

/**
 * Class m191218_095933_copany_u1
 */
class m191218_095933_copany_u1 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company','accounts',$this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_095933_copany_u1 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_095933_copany_u1 cannot be reverted.\n";

        return false;
    }
    */
}
