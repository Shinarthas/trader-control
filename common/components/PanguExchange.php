<?
namespace common\components;
use common\models\Task;
use common\models\AccountBalance;
use common\models\Currency;
use common\models\RateHistory;
use common\models\TaskAdditional;

class PanguExchange {
	
	const CONTRACT_ADDRESS = '41ba78d7f021b0ff6e19f5b9c30392c0b9ec319f21';
	
	// текущий курс продажи и покупки?
	public static function sellOrder($currency_one, $currency_two, $tokens_count, $price, $account) {
		
		$proxy = $account->proxy;
		
		// approve tokens
		$parameters = [
			ETC::hexTo64bitHex(substr(self::CONTRACT_ADDRESS,2)),
			ETC::decTo64bitHex($tokens_count* 10**$currency_two->decimals)
		];

		$result = ETC::triggerContract($currency_two->address, 0, 'approve(address,uint256)', $parameters, $account->data, $proxy);
		
		if(!$result)
			return false;
		
		$rate = $price* 10**$currency_one->decimals;
		$value = $rate*$tokens_count;
		
		$function = 'sell(uint16,uint256,uint256)';
		$parameters = [
			ETC::decTo64bitHex($currency_two->data['pangu_id']),
			ETC::decTo64bitHex($rate),
			ETC::decTo64bitHex($tokens_count* 10**$currency_two->decimals)
		];
	
		
		return ETC::triggerContract(self::CONTRACT_ADDRESS, 0, $function, $parameters, $account->data, $proxy, 1);
	}
	
	// текущий курс продажи и покупки?
	public static function buyOrder($currency_one, $currency_two, $tokens_count, $price, $account) {

		$rate = $price* 10**$currency_one->decimals;
		$value = $rate*$tokens_count;
		
		$function = 'buy(uint16,uint256)';
		$parameters = [
			ETC::decTo64bitHex($currency_two->data['pangu_id']),
			ETC::decTo64bitHex($rate)
		];
		
		return ETC::triggerContract(self::CONTRACT_ADDRESS, $value, $function, $parameters, $account->data, $account->proxy, 1);
	}
	
	public static function exchangeRates($currency_one, $currency_two) {
		
		$data = json_decode(file_get_contents("http://api.pangu.trade/v1/currency/best-orders?currency_id=".$currency_two->data['pangu_id']), true);
		
		$top_buy = 0;
		$top_sell = 10**10;
		
		foreach($data['buy_orders'] as $item) {
		//	RateHistory::create($currency_one->id, $currency_two->id, 0, 1, $item);
			if($top_buy < $item['price'])
				$top_buy = $item['price'];
		}
		
		foreach($data['sell_orders'] as $item) {
		//	RateHistory::create($currency_one->id, $currency_two->id, 1, 1, $item);
			if($top_sell > $item['price'])
				$top_sell = $item['price'];
		}

		return [
			'buy_price' => $top_buy,
			'sell_price' => $top_sell
		];
	}
	
	public static function loadUncompletedOrders($promotion, $account) {
		$currencies = [];;
	/*	
		foreach(Currency::find()->all() as $c) {
			if($c->data['pangu_id']!=0)
				$currencies[$c->data['pangu_id']] = $c->id;
		}
	
		$data = json_decode(file_get_contents("http://pangu-api/v1/user/active-orders?id=".$account->data['pangu_id']), true)['data'];
		
		$currencies_array = [];
		
		
		$currencies1_balance = [];
		$currencies2_balance = [];
		
		foreach($data as $order) {
		
			$task_id = 0;
			
			if($currencies_array[$order['fShortName']] == 0) {
				$currencies_array[$order['fShortName']] = Currency::findOne(['symbol'=>$order['fShortName']])->id;
			}
			$currency2id = $currencies_array[$order['fShortName']];
		
			if($order['orderType'] == 1) 
				$currencies2_balance[$currency2id] += $order['volume'] * (1-$order['schedule']);
			else 
				$currencies1_balance[$currency2id] += $order['price']*$order['volume'] * (1-$order['schedule']);
			
			
			if($t = Task::find()->where(['promotion_id'=>$promotion->id, 'account_id'=>$account->id, 'rate'=>$order['price'], 'sell' => $order['orderType']])->andWhere(["BETWEEN", "created_at", ((int)($order['orderTime']/1000) - 200) ,(int)($order['orderTime']/1000) ])->one()){
				$t->progress = (int)($order['schedule']*100);
				$t->data = ['order_id' => $order['orderID']];
				$t->loaded_at = time();
				$t->status = Task::STATUS_CREATED;
				$t->save();
				
				$task_id = $t->id;
			}
			
					
			$ta = new TaskAdditional();
			$ta->task_id = $task_id;
			$ta->type = 1;
			$ta->account_id = $account->id;
			$ta->sell = $order['orderType'];
			$ta->data = ['order_id' => $order['orderID']];
			$ta->created_at = time();
			$ta->rate = $order['price'];
			$ta->save();
		}
		
		foreach($currencies1_balance as $currency2_id => $currency1_balance) {
			$b = new AccountBalance;
			$b->account_id = $account->id;
			$b->currency_id = 1;
			$b->value = $currency1_balance;
			$b->type = AccountBalance::TYPE_TRONSCAN_EXCHANGER;
			$b->loaded_at = time();
			$b->currency_two = $currency2_id;
			$b->save();
		}
		
		foreach($currencies2_balance as $currency_id => $currency2_balance)
		{
			$b = new AccountBalance;
			$b->account_id = $account->id;
			$b->currency_id = $currency_id;
			$b->value = $currency2_balance;
			$b->type = AccountBalance::TYPE_TRONSCAN_EXCHANGER;
			$b->loaded_at = time();
			$b->save();
		}
		
		foreach(Task::find()->where(['promotion_id'=>$promotion->id, 'status'=>Task::STATUS_CREATED])->andWhere(['<', 'loaded_at', time()-60])->andWhere(['!=', 'progress',100])->all() as $task) {
			$task->progress = 100;
			$task->save();
		}*/
	}
	
	public static function cancelOrder($account, $task) {
		$function = 'cancelOrder(uint256)';
		
		$parameters = [ ETC::decTo64bitHex($task->data['order_id']) ];
		
		return ETC::triggerContract(self::CONTRACT_ADDRESS, 0, $function, $parameters, $account->data, $account->proxy, 1);
	}
}


?>