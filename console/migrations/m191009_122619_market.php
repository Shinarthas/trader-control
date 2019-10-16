<?php

use yii\db\Migration;

/**
 * Class m191009_122619_market
 */
class m191009_122619_market extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableName = Yii::$app->db->tablePrefix . 'market';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable($tableName);
        }
        $this->createTable($tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'url' => $this->string()->notNull(),
			'image' => $this->string()->notNull(),
            'created_at' =>  $this->integer()->notNull(),
        ]);
        $c=['name'=>'Trx Market','url'=>'https://trx.market/','image'=>'/images/trx.market.png','created_at'=>time()];
        $this->insert($tableName, $c);
		
		$c=['name'=>'Tron Trade','url'=>'https://trontrade.io/','image'=>'/images/tt.png','created_at'=>time()];
        $this->insert($tableName, $c);
		
		$c=['name'=>'Binance','url'=>'https://binance.com/','image'=>'/images/binance.svg','created_at'=>time()];
        $this->insert($tableName, $c);
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191009_122619_market cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191009_122619_market cannot be reverted.\n";

        return false;
    }
    */
}
