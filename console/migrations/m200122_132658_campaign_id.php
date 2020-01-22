<?php

use yii\db\Migration;

/**
 * Class m200122_132658_campaign_id
 */
class m200122_132658_campaign_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('task','group_id','campaign_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200122_132658_campaign_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200122_132658_campaign_id cannot be reverted.\n";

        return false;
    }
    */
}
