<?php

use yii\db\Migration;

/**
 * Class m191119_142137_name_image_for_currency
 */
class m191119_142137_name_image_for_currency extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('currency','name',$this->string());
        $this->addColumn('currency','image',$this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('currency','name');
        $this->dropColumn('currency','image');
        echo "m191119_142137_name_image_for_currency cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191119_142137_name_image_for_currency cannot be reverted.\n";

        return false;
    }
    */
}
