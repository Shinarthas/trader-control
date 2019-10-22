<?php

use yii\db\Migration;

/**
 * Class m191022_103321_task_modification
 */
class m191022_103321_task_modification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('task', 'canceled', 'int(11) AFTER external_id'); 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191022_103321_task_modification cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191022_103321_task_modification cannot be reverted.\n";

        return false;
    }
    */
}
