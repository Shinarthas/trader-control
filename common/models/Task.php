<?php

namespace common\models;

use Yii;
use common\components\ApiRequest;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $account_id
 * @property int $status
 * @property int $sell
 * @property int $value
 * @property double $random_curve
 * @property string $tokens_count
 * @property string $rate
 * @property int $progress
 * @property string $data_json
 * @property string $external_id
 * @property int $time
 * @property int $created_at
 * @property int $loaded_at
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }
	
	const STATUS_NEW = 0;
	const STATUS_STARTED = 1;
	const STATUS_CREATED = 2;
	const STATUS_PRICE_ERROR = 3;
	const STATUS_CANCELED = 4;
	const STATUS_COMPLETED = 5;
	const STATUS_ACCOUNT_NOT_FOUND = 11;
	
	
	public static $statuses = [
		self::STATUS_NEW => 'new',
		self::STATUS_STARTED => 'error',
		self::STATUS_CREATED => 'created',
		self::STATUS_PRICE_ERROR => 'price error',
		self::STATUS_CANCELED => 'canceled by system',
		self::STATUS_COMPLETED => 'completed',
		self::STATUS_ACCOUNT_NOT_FOUND => 'account not found',
	];
	
	const MODE_STABILIZE = 0;
	const MODE_INCREASE = 1;
	const MODE_DECREASE = 2;
	const MODE_PASSIVE = 3;
	
	public static $modes = [
		self::MODE_STABILIZE => 'stabilize price',
		self::MODE_INCREASE => 'increase price',
		self::MODE_DECREASE => 'decrease price',
		self::MODE_PASSIVE => 'passive mode'
	];
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id',  'value', 'random_curve', 'time'], 'required'],
            [['canceled','promotion_id', 'account_id', 'status', 'sell', 'value', 'progress', 'time', 'created_at', 'loaded_at'], 'integer'],
            [['random_curve', 'tokens_count', 'rate'], 'number'],
            [['data_json'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Promotion ID',
            'account_id' => 'Account ID',
            'status' => 'Status',
            'sell' => 'Sell',
            'value' => 'Value',
            'random_curve' => 'Random Curve',
            'tokens_count' => 'Tokens Count',
            'rate' => 'Rate',
            'progress' => 'Progress',
            'data_json' => 'Data Json',
            'time' => 'Time',
            'created_at' => 'Created At',
            'loaded_at' => 'Loaded At',
        ];
    }
	
	
	public function make() {

		$this->status = self::STATUS_STARTED;
	
		$promotion = $this->promotion;
		if($promotion->enabled == 0)
			return false;

		$this->save();
		if(!$res=$this->calculateRate())
		{
			$this->status = self::STATUS_PRICE_ERROR;
			$this->save();
			return false;
		}
		if($promotion->market_id==4)
		{
			if($promotion->second_currency->data['pangu_step']==10)
				$this->rate = round($this->rate,5);
			elseif($promotion->second_currency->data['pangu_step']==100)
				$this->rate = round($this->rate,4);
			elseif($promotion->second_currency->data['pangu_step']==1000)
				$this->rate = round($this->rate,3);
			else
				$this->rate = round($this->rate,2);
		}
		
		$tokens_count = round($this->value/$this->rate, 1);
		
		if($this->sell == 1 AND (int)$promotion->settings['fixed_tasks_currency_two']!=0) {
			$tokens_count = $promotion->settings['fixed_tasks_currency_two'];
		}

		if($promotion->settings['calculate_account']!=1)
			$account = $this->promotion->accounts[array_rand($this->promotion->accounts,1)];
		else
		{
			if(!$account = $promotion->calculateAccount())
			{
				$this->status = self::STATUS_ACCOUNT_NOT_FOUND;
				$this->save();
				return false;
			}
		}
			
		$this->account_id = $account->id;
		
		$this->tokens_count = $tokens_count;
  	$result = ApiRequest::accounts('v1/orders/create', 
			[
			'id'=>$this->id,
			'sell'=>$this->sell,
			'market_id'=>$promotion->market_id,
			'currency_one'=>$promotion->currency_one, 
			'currency_two' => $promotion->currency_two, 
			'account_id' => $this->account_id,
			'tokens_count' => $this->tokens_count,
			'created_at' => $this->created_at,
			'promotion_id' => $this->promotion_id,
			'rate' => $this->rate,
			'use_paid_proxy' => $this->promotion->is_paid_proxy,
			]);
		if($result->status) {
			$this->status = self::STATUS_CREATED;
			//$this->progress = 100;
			$this->created_at = time();
            if(isset($result->data->external_id)){
                $this->external_id=$result->data->external_id;
            }
            //update same info on statistics serve
            $resultStatistics = ApiRequest::statistics('v1/orders/create',
                ArrayHelper::toArray($this));
		}

		$this->save();
		
		return $result;
	}

	public static function possibility(Promotion $promotion){

        $account = $promotion->accounts[array_rand($promotion->accounts,1)];


        //получим текущий  баланс
        $balance = ApiRequest::statistics('v1/account/get-balance-now', ['id'=>$account->id]);
        // получим валюту которую мы хотим продать в долларах
        $currency_one_usdt_rate=ApiRequest::statistics('v1/currency/usdt-rate',['id'=>$promotion->currency_one]);
        //а почем ее продают сейчас?
        $exchange_rates=ApiRequest::statistics('v1/exchange-course/get-course',
            [
                'market_id'=>$promotion->market_id,
                'currency_one'=>$promotion->currency_one,
                'currency_two'=>$promotion->currency_two,
            ]);

        $how_many_usdt_we_need=$balance->data->in_usd*$promotion->settings['maximal_stake']/100;
        //find our currency;
        $amount_free=0;
        $amount_in_orders=0;
        foreach ($balance->data->balances as $currency_balance){
            if($currency_balance->currency_id==$promotion->currency_one){
                $amount_free=$currency_balance->value;
                $amount_in_orders=$currency_balance->value_in_orders;
            }

        }
        $how_many_usdt_we_have=$amount_free*$currency_one_usdt_rate->data->rate;
        $how_many_usdt_we_have_in_orders=$amount_in_orders*$currency_one_usdt_rate->data->rate;

        //проверка поставили ли мы уже этот ордер?
        if($how_many_usdt_we_have_in_orders>$how_many_usdt_we_need)
            return;

        if ($how_many_usdt_we_have_in_orders+$how_many_usdt_we_have>=$how_many_usdt_we_need && $how_many_usdt_we_have<$how_many_usdt_we_need){
            //у нас есть столько денег но они в ордерах, нужноих  отменить
            // или ты уже поставил эти ордера
            echo 'gavno';
            //return;
            //sleep(3);//чтоб ордер точно прошел
        }
        if($how_many_usdt_we_need>$how_many_usdt_we_have){
            //у нас нет столько нужно купить
            $how_many_we_need_to_buy=($how_many_usdt_we_need-$how_many_usdt_we_have)*1.01;
            $task=new Task();
            $task->account_id = $account->id;
            $task->promotion_id=$promotion->id;
            $task->status=0;
            $task->sell=0;
            $task->rate=$exchange_rates->data->sell_price*1.002;// 10%
            $task->tokens_count=$how_many_we_need_to_buy;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
            $task->random_curve=0;
            $task->value=intval($task->tokens_count*$task->rate);
            $task->progress=0;
            $task->time=time();

            $task->save();
            echo $task->id;

            $task->make();

            sleep(3);//чтоб ордер точно прошел
        }
        $task=new Task();
        $task->account_id = $account->id;
        $task->promotion_id=$promotion->id;
        $task->status=0;
        $task->sell=1;
        $task->rate=$exchange_rates->data->sell_price*1.10;// 10%
        $task->tokens_count=$how_many_usdt_we_need/$exchange_rates->data->sell_price;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
        $task->random_curve=0;
        $task->value=intval($task->tokens_count*$task->rate);
        $task->progress=0;
        $task->time=time();

        $task->save();
        echo $task->id;

        $task->make();

        // поставить ордер


    }
	
	public function calculateRate() {
		$promotion = $this->promotion;
		if(!$price = CurrencyPrice::currentPrice($promotion->market_id, $promotion->currency_one, $promotion->currency_two, 600,0))
            return false;

		$timeout = 900;
		
		if($promotion->market_id==2)
			$timeout = 10000;
		if(time() - $price->created_at > $timeout)
			return false;

		$random_rate = 1;
		if(rand(0,2) != 0)
			$random_rate = ((100 + $this->random_curve) / 100);

		if($promotion->mode == Promotion::MODE_FAST_VOLUME) {
			$this->rate = ($price->buy_price + $price->sell_price)/2;
			$temp_rate = $this->rate * $random_rate;
			
			if($temp_rate > $price->buy_price AND $temp_rate < $price->sell_price)
				$this->rate = $temp_rate;
		}
		else if($promotion->mode == Promotion::MODE_USER_SIMULATOR) {
			if($this->sell == 1)
				$this->rate = $price->buy_price;
			else
				$this->rate = $price->sell_price;
		}
		else if($promotion->mode == Promotion::MODE_INCREASE || $promotion->mode == Promotion::MODE_STABILIZE){
			
			
			$temp_rate = $promotion->settings['price_threshold'] * $random_rate * ((100+ ( ((time() - $promotion->started_at)/(3600*24))*$promotion->settings['speed'] ) )/100);
			
			if($this->sell == 1) {
				// if our price lower than market price - sell on avg makret price
				if($temp_rate < $price->sell_price) 
					$this->rate = $price->sell_price - 0.0001;
				
				else if($temp_rate*((100 - $promotion->settings['price_stabilize_power'])/100) > $price->sell_price)
					$this->rate = $temp_rate*((100 - $promotion->settings['price_stabilize_power'])/100);
				else
					$this->rate = $price->sell_price;

			} else {	
				// if buy price more than market price - set price equal to sell price
				if($temp_rate >= $price->sell_price)
					$this->rate = $price->sell_price;
				else
					$this->rate = $temp_rate;
			}
			
		}
		else if($promotion->mode == Promotion::MODE_VOLUME_INCREASE){
			$small_difference = 0.0001;
			if($this->rate<0.01)
				$small_difference = 0.000001;
			
		
			if($this->sell == 1)
				$this->rate = $price->sell_price - $small_difference;
			else
				$this->rate = $price->buy_price + $small_difference;
		}
		else if($promotion->mode == Promotion::MODE_JUST_BUY){
			$this->rate = $price->buy_price + (rand(1,10)/10);
		}
		else if($promotion->mode == Promotion::MODE_PERCENT_EARN || $promotion->mode == Promotion::MODE_SAFE_EXIT){
			$small_difference = 0.0001;
			if($this->rate<0.01)
				$small_difference = 0.000001;
				
			
			$sell_rate = 1 + ($promotion->settings['earn_percent']/100);
			$buy_rate  = 1 - ($promotion->settings['earn_percent']/100);
				
			$rand_again = rand(0,10);
			if(!$currency = CurrencyPrice::currentPrice($promotion->market_id, $promotion->currency_one, $promotion->currency_two, 600,0)){
			    return false;
            }

			if($this->sell == 1) {
				$this->rate = $price->buy_price*$sell_rate < $price->sell_price - $small_difference ? $price->sell_price - $small_difference : $price->buy_price*$sell_rate;
			
				$difference = $currency->sell_price - $this->rate;
				if($difference/$currency->sell_price > 0.08)
					return false;
					
				if($rand_again == 1)
					$this->rate*= 1.03;
				if($rand_again == 0)
					$this->rate*= 0.995;
			}
			else {
				$this->rate = $price->sell_price*$buy_rate > $price->buy_price + $small_difference ? $price->buy_price + $small_difference : $price->sell_price*$buy_rate;
				
				$difference = $this->rate - $currency->buy_price;
				if($difference/$currency->buy_price > 0.08)
					return false;
					
				if($rand_again == 1)
					$this->rate*= 0.98;
				if($rand_again == 0)
					$this->rate*= 1.005;
			}
		}
		else if($promotion->mode == Promotion::MODE_FAST_EARN) {
			$small_difference = 0.0001;
			if($this->rate<0.01)
				$small_difference = 0.000001;
				
			$small_rand = (rand(0,40)-20)/10000;
			
			$sell_rate = 1 + ($promotion->settings['earn_percent']/100);
			$buy_rate  = 1 - ($promotion->settings['earn_percent']/100);
			
			$sell_rate+= $small_rand;
			$buy_rate+= $small_rand;
			

			if(!$currency = CurrencyPrice::currentPrice($promotion->market_id, $promotion->currency_one, $promotion->currency_two, 600, 0))
				return false;
					
			if($this->sell == 1) {
				$this->rate = $price->buy_price*$sell_rate < $price->sell_price - $small_difference ? $price->sell_price - $small_difference : $price->buy_price*$sell_rate;
			
				$difference = $currency->sell_price - $this->rate;
				if($difference/$currency->sell_price > 0.08)
					return false;
			}
			else {
				$this->rate = $price->sell_price*$buy_rate > $price->buy_price + $small_difference ? $price->buy_price + $small_difference : $price->sell_price*$buy_rate;
				
				$difference = $this->rate - $currency->buy_price;
				if($difference/$currency->buy_price > 0.08)
					return false;
			}
		}
		else if($promotion->mode == Promotion::MODE_PUMP_DUMP) {
		
			$increase_rate = ((100+ ( ((time() - $promotion->started_at)/(3600*24))*$promotion->settings['speed'] ) )/100);
			$random_pump_dupm = rand(0,40)/1000;
			
			if($this->sell == 1)
				$this->rate = $price->buy_price * $increase_rate * (1 - $random_pump_dupm);
			else
				$this->rate = $price->sell_price * $increase_rate * (1 + $random_pump_dupm);
		}
		$this->rate = round($this->rate,5);
		return true;
	}
	
	public function cancelOrder() {
        $res=ApiRequest::accounts( 'v1/orders/cancel', [ 'id' => $this->id, 'external_id'=>$this->external_id,'use_paid_proxy' => $this->promotion->is_paid_proxy, ]);
        if($res->status){
            $this->status=Task::STATUS_CANCELED;
            $this->canceled = 1;
            $this->save();
            $res1=ApiRequest::statistics('v1/orders/update', ['id'=>$this->id,'canceled'=>$this->canceled ,'external_id'=>$this->external_id,'progress'=>$this->progress, 'status' => $this->status]);

        }

		return $res;
	}
	
	public function getPromotion() {
		return $this->hasOne(Promotion::className(), ['id'=>'promotion_id']);
	}
	
	public function getAccount() {
		return $this->hasOne(Account::className(), ['id'=>'account_id']);
	}
	
	public function getData($assoc = true)
    {
        return json_decode($this->data_json,$assoc);
    }
	 
    public function setData($data)
    {
        $this->data_json = json_encode($data);
    }
}
