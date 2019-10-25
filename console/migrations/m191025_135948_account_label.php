<?php

use yii\db\Migration;

/**
 * Class m191025_135948_account_label
 */
class m191025_135948_account_label extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('account', 'label', $this->string()->notNull()); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191025_135948_account_label cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191025_135948_account_label cannot be reverted.\n";

        return false;
    }
    */
}
