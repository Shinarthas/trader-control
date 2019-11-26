<?php

namespace common\models;

use common\components\ApiRequest;
use Yii;

/**
 * This is the model class for table "currency_price".
 *
 * @property int $id
 * @property int $currency_one
 * @property int $currency_two
 * @property int $platform_id
 * @property string $buy_price
 * @property string $sell_price
 * @property int $created_at
 */
class CurrencyPrice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['currency_one', 'currency_two', 'market_id', 'buy_price', 'sell_price', 'created_at'], 'required'],
            [['currency_one', 'currency_two', 'market_id', 'created_at'], 'integer'],
            [['buy_price', 'sell_price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency_one' => 'Currency One',
            'currency_two' => 'Currency Two',
            'market_id' => 'Market ID',
            'buy_price' => 'Buy Price',
            'sell_price' => 'Sell Price',
            'created_at' => 'Created At',
        ];
    }
	
	public static function avgPrice($market_id, $currency_one, $currency_two) {
	
		 $res=ApiRequest::statistics('v1/exchange-course/current-course',[
            'market_id'=>$market_id,
            'currency_one'=>$currency_one,
            'currency_two'=>$currency_two,
            'from'=>1000,
            'to'=>0,
        ]);
        if ($res->status) {
			$data = $res->data;
			 
            return ($data->buy_price + $data->sell_price)/2;
		}
        else
            return 0;
	}
	
	public static function currentPrice($market_id, $currency_one, $currency_two, $from=900, $to=600) {//BETWEEN time()-900 < TIME < time()-600
	//	return self::find()->where(['market_id'=>$market_id, 'currency_one'=>$currency_one, 'currency_two'=>$currency_two])->orderBy("id DESC")->one();
	
        //API REQUEST 
        $res=ApiRequest::statistics('v1/exchange-course/current-course',[
            'market_id'=>$market_id,
            'currency_one'=>$currency_one,
            'currency_two'=>$currency_two,
            'from'=>$from,
            'to'=>$to,
        ]);//offset in seconds
        if ($res->status)
            return $res->data;
        else
            return 0;
		//return self::find()->where(['market_id'=>$market_id, 'currency_one'=>$currency_one, 'currency_two'=>$currency_two])->orderBy("id DESC")->one();
	}
}
