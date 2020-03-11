<?php

use yii\db\Migration;

/**
 * Class m200228_152259_graph
 */
class m200228_152259_graph extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('guessing',[
            'id'=>$this->primaryKey(),
            'timestamp'=>$this->integer(),
            'percent'=>$this->double(),
            'guessed'=>$this->integer(),
            'total'=>$this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200228_152259_graph cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200228_152259_graph cannot be reverted.\n";

        return false;
    }
    */
}
