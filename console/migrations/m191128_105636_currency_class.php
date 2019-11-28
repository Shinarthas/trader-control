<?php

use yii\db\Migration;

/**
 * Class m191128_105636_currency_class
 */
class m191128_105636_currency_class extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('currency','class',$this->string()->defaultValue("GoodCurrency"));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('currency','class');
        echo "m191128_105636_currency_class cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191128_105636_currency_class cannot be reverted.\n";

        return false;
    }
    */
}
