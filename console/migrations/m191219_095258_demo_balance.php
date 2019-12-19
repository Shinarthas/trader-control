<?php

use yii\db\Migration;

/**
 * Class m191219_095258_demo_balance
 */
class m191219_095258_demo_balance extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('demo_balance',[
            'id'=>$this->primaryKey(),
            'balances'=>$this->json(),
            'timestamp'=>$this->timestamp()
        ]);

        $this->insert('demo_balance',[
            'id'=>1,
            'balances' =>['USDT'=>['tokens'=>1000000,'value'=>1000000]],
            'timestamp'=>date('Y-m-d H:i:s')
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191219_095258_demo_balance cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191219_095258_demo_balance cannot be reverted.\n";

        return false;
    }
    */
}
