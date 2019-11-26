<?php

namespace common\models;

use common\components\ApiRequest;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "promotion".
 *
 * @property int $id
 * @property string $name
 * @property int $market_id
 * @property int $enabled
 * @property int $mode
 * @property string $settings_json
 * @property int $started_at
 * @property int $created_at
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion';
    }
	const MODE_STABILIZE = 0;
	const MODE_INCREASE = 1;
	const MODE_USER_SIMULATOR = 2;
	const MODE_FAST_VOLUME = 3;
	const MODE_VOLUME_INCREASE = 4;
	const MODE_PERCENT_EARN = 5;
	const MODE_FAST_EARN = 6;
	const MODE_PUMP_DUMP = 7;
	
	// don't create buy orders, equal to MODE_PERCENT_EARN
	const MODE_SAFE_EXIT = 10;
	
	const MODE_JUST_BUY = 20;
	
	public static $modes = [
		self::MODE_STABILIZE => 'stabilize price',
		self::MODE_INCREASE => 'increase price',
		self::MODE_USER_SIMULATOR => 'user simulator',
		self::MODE_FAST_VOLUME => 'fast volume increase',
		self::MODE_VOLUME_INCREASE => 'smart volume increase',
		self::MODE_PERCENT_EARN => '2% earning',
		self::MODE_FAST_EARN => '1% earning',
		self::MODE_SAFE_EXIT => 'safe exit',
		self::MODE_JUST_BUY => 'just buy',
		self::MODE_PUMP_DUMP => 'pumping/dumping',
	];
		public static $frequency_variants = [
			'1',
			'3-5',
			'2-7',
			'5-10',
			'9-16',
			'15-25',
			'20-30',
			'25-40',
			'30-50',
			'40-60',
			'50-80',
			'60-90',
			'70-100',
			'90-120',
			'100-150',
			'8',
			'10',
			'12',
			'15',
			'20',
			'25',
			'2',
			'3'
		];
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_one','currency_two', 'name', 'market_id',  'mode', 'settings_json',  'created_at'], 'required'],
            [['currency_one','currency_two', 'market_id', 'enabled', 'mode', 'started_at', 'created_at'], 'integer'],
            [['settings_json'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'market_id' => 'Market ID',
            'enabled' => 'Enabled',
            'mode' => 'Mode',
            'settings_json' => 'Settings Json',
            'started_at' => 'Started At',
            'created_at' => 'Created At',
        ];
    }
	
	public function start() {
		if($this->settings['day_tasks']==1)
			$this->createDayTasks(floor(time()/86400)*86400);
		else
			$this->createHourTasks(floor(time()/3600)*3600);
		
		$this->enabled = 1;
		$this->started_at = time();
		$this->save();		
	}
	
	public function stop() {
		$this->enabled = 0;
		$this->save();
		
		foreach(Task::find()->where(['>','time',time()])->andWhere(['promotion_id'=>$this->id])->all() as $t)
			$t->delete();
	}
	
	
	public function createDayTasks($start_time) {
		self::createHourTasks($start_time, true);
	}
	
	public function createHourTasks($start_time, $day_tasks = false) {
		
		$multiply = 60;
		if($day_tasks)
			$multiply = 60*24;
			
		$currency_overflow_rate = 1;
		
		$count_tasks_limits = preg_split('|\-|',Promotion::$frequency_variants[$this->settings['frequency']]);
		if($count_tasks_limits[1] == 0)
			$count_tasks = $count_tasks_limits[0];
		else
			$count_tasks = rand($count_tasks_limits[0], $count_tasks_limits[1]);

			
		if($this->mode != self::MODE_SAFE_EXIT) {
			for($i=0;$i<$count_tasks;$i++) {
				
				$t = new Task();
				
				$period_time = $multiply / $count_tasks;
				$t->time = $start_time+($i*$period_time*60);
				$t->time+= rand(0, $period_time)*60;
				
				if($t->time < time())
					continue;
				
				$t->time = (int)$t->time;
				$t->value = $this->settings['hour_volume']/$count_tasks;
				
				// calculate additonal currency rate
				$t->value*= (rand(5,15)/10) * $currency_overflow_rate;
					
				$t->value = round($t->value);
				$t->promotion_id = $this->id;
				$t->sell = 0;
				$t->random_curve = rand(0,60)/100 - 0.3;
				$t->save();
			}
		}
		
			
		if($this->mode != self::MODE_JUST_BUY) {
			for($i=0;$i<$count_tasks;$i++) {
				
				$t = new Task();
				
				$period_time = 60 / $count_tasks;
				$t->time = $start_time+($i*$period_time*60);
				$t->time+= rand(0, $period_time)*60;
				
				if($t->time < time())
					continue;
				
				$t->time = (int)$t->time;
				$t->value = $this->settings['hour_volume']/$count_tasks;
				$t->value*= (rand(5,15)/10);
				$t->value = round($t->value);
				$t->promotion_id = $this->id;
				$t->sell = 1;
				$t->random_curve =rand(0,200)/100 - 1;
				$t->save();
			}
		}
	}
	
		public function clearOldOrders() {
	
		if( $this->mode == Promotion::MODE_PERCENT_EARN OR
			$this->mode == Promotion::MODE_FAST_EARN OR
			$this->mode == Promotion::MODE_SAFE_EXIT
		){
			return $this->newClearOrders();
		}
		
		$count_tasks_limits = preg_split('|\-|',Promotion::$frequency_variants[$this->settings['frequency']]);
		$orders_limit = $count_tasks_limits[1];
		
		if($orders_limit == 0)
			$orders_limit = $count_tasks_limits[0];
			
		if($this->settings['order_cancel'] != 0)
			$orders_limit = $this->settings['order_cancel'];
		
		$count_sell_orders = Task::find()->select("count(id) as count")->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>1])->andWhere(['>', 'loaded_at', time()-900])->scalar();
		
		//echo "count sell orders: ".$count_sell_orders.'<br>';
		$count_buy_orders = Task::find()->select("count(id) as count")->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>0])->andWhere(['>', 'loaded_at', time()-900])->scalar();
		
		//echo "count buy orders: ".$count_buy_orders.'<br>';
		
		foreach(Task::find()->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>1])->andWhere(['>', 'loaded_at', time()-900])->orderBy("rate DESC")->limit($count_sell_orders - $orders_limit)->all() as $task)
			if(rand(0,3) != 3)
				$task->cancelOrder();
		
		foreach(Task::find()->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>0])->andWhere(['>', 'loaded_at', time()-900])->orderBy("rate")->limit($count_buy_orders - $orders_limit)->all() as $task)
			if(rand(0,3) != 3)
				$task->cancelOrder();
	}
	
	public function newClearOrders() {
		$currency_price = CurrencyPrice::avgPrice($this->market_id, $this->currency_one, $this->currency_two);
		if($currency_price == 0)
			return false;
		
		$orders_width = 0.020;
		if($this->mode == self::MODE_FAST_EARN)
			$orders_width = 0.005;

		// calculating trasholds for sell orders
		$sell_lower_rate = $currency_price*(1 + (($this->settings['earn_percent']/2)/100));
		$sell_upper_rate = $sell_lower_rate*(1 + $orders_width);
		
		$tasks2 = Task::find()->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>1])->andWhere(['>','rate',$sell_upper_rate])->andWhere(['>', 'loaded_at', time()-1100])->all();
		
		// calculating trasholds for buy orders
		$buy_upper_rate  = $currency_price*(1 - (($this->settings['earn_percent']/2)/100));
		$buy_lower_rate = $buy_upper_rate*(1 - $orders_width);
		
		$tasks4 = Task::find()->where(['promotion_id'=>$this->id, 'status'=>2, 'sell'=>0])->andWhere(['<','rate',$buy_lower_rate])->andWhere(['>', 'loaded_at', time()-1100])->all();
		
		$tasks = array_merge($tasks2, $tasks4);
		
		foreach($tasks as $task) {
			if($this->mode == self::MODE_FAST_EARN OR rand(0,1 == 1))
				$task->cancelOrder();
		}
	}
	
	public function checkPrice() {

        $res=ApiRequest::statistics('v1/exchange-course/get-course',ArrayHelper::toArray($this));
		if(!$res->status){
            //TODO:log error
        }


		$data = json_decode(json_encode($res->data),true);

		//uncomment  if you decide to save it on this server
	/*	$exchanger = '\\common\\components\\' .$this->market->class;
		
		$data = $exchanger::exchangeRates($this->main_currency, $this->second_currency);
		
		$rates = new CurrencyPrice;
		$rates->currency_one = $this->currency_one;
		$rates->currency_two = $this->currency_two;
		$rates->market_id = $this->market_id;
		$rates->buy_price = $data['buy_price'];
		$rates->sell_price = $data['sell_price'];
		$rates->created_at = time();
		$rates->save();*/
	}
	
	public function getPromotionAccounts() {
		return $this->hasMany(PromotionAccount::className(), ['promotion_id'=>'id']);
	}
	
	public function getErrors_percent() {
		$errors = 0;
		$success = 0;
		foreach(Task::find()->where(['promotion_id'=>$this->id])->andWhere(["!=",'status',0])->orderBy("id DESC")->limit(10)->all() as $t)
		{
			if($t->status == $t::STATUS_CREATED)
				$success++;
			else
				$errors++;
		}
		return round(($errors/($success+$errors))*100,2);
	}
	
	public function getAccounts() {
		 return $this->hasMany(Account::className(), ['id' => 'account_id'])
            ->via('promotionAccounts');
	}
	
	public function getSettings($assoc = true)
    {
        return json_decode($this->settings_json,$assoc);
    }
	 
    public function setSettings($data)
    {
        $this->settings_json = json_encode($data);
    }
	
	public function getMain_currency() {
		return $this->hasOne(Currency::className(), ['id'=>'currency_one']);
	}
	
	public function getSecond_currency() {
		return $this->hasOne(Currency::className(), ['id'=>'currency_two']);
	}
	
	public function getTasks() {
		return $this->hasMany(Task::className(), ['promotion_id'=>'id'])->orderBy("task.time DESC")->limit(30);
	}
	
	public function getMarket() {
		return $this->hasOne(Market::className(), ['id'=>'market_id']);
	}
}
