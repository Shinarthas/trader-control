<?php

use yii\db\Migration;

/**
 * Class m191227_103758_entrace_currency
 */
class m191227_103758_entrace_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('company','entrance_currency',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191227_103758_entrace_currency cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191227_103758_entrace_currency cannot be reverted.\n";

        return false;
    }
    */
}
