<?php

use yii\db\Migration;

/**
 * Class m191128_151126_is_paid_proxy
 */
class m191128_151126_is_paid_proxy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('promotion','is_paid_proxy',$this->boolean()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('promotion','is_paid_proxy');
        echo "m191128_151126_is_paid_proxy cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191128_151126_is_paid_proxy cannot be reverted.\n";

        return false;
    }
    */
}
