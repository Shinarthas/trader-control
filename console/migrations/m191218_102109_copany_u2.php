<?php

use yii\db\Migration;

/**
 * Class m191218_102109_copany_u2
 */
class m191218_102109_copany_u2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company','name',$this->string());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191218_102109_copany_u2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191218_102109_copany_u2 cannot be reverted.\n";

        return false;
    }
    */
}
