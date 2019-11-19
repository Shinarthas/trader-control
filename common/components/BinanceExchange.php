<?
namespace common\components;
use common\models\Account;
use common\models\Order;
use common\models\Task;
use common\models\AccountBalance;
use common\models\Currency;
use common\models\RateHistory;
use common\models\TaskAdditional;
use Binance;
class BinanceExchange {
	
	const CONTRACT_ADDRESS = '41b3bddae866b2ce2349bdcb59dfbfa1a75f8552da';

	const BINANCE=3;
	// текущий курс продажи и покупки?
	public static function sellOrder($currency_one, $currency_two, $tokens_count, $price, $account) {
		if($account->type!=self::BINANCE)
		    return 0;

		$proxy = $account->proxy;
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        //TODO:support proxy
        $order = $api->sell($currency_pair, $tokens_count, $price);
        return $order;
	}
	
	// текущий курс продажи и покупки?
	public static function buyOrder($currency_one, $currency_two, $tokens_count, $price, $account) {

        if($account->type!=self::BINANCE)
            return 0;

        $proxy = $account->proxy;
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        //TODO:support proxy
        $order = $api->buy($currency_pair, $tokens_count, $price);
        return $order;
	}
	
	public static function exchangeRates($currency_one, $currency_two) {
        $api = new Binance\API();
        //TODO: Заменить на валюты из БД и сущности на модели
        $currency_pair=$currency_one.$currency_two;
        $depth = $api->depth($currency_pair);
        return [
            'buy_price' => array_key_first($depth['asks']),
            'sell_price' => array_key_first($depth['bids'])
        ];
	}
	
	public static function loadUncompletedOrders($promotion, $account) {
        if($account->type==Account::BINANCE)
            return 0;//это не аккаунт бинанса
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_one=Currency::findOne($promotion['currency_one']);
        $currency_two=Currency::findOne($promotion['currency_two']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol
        $openorders = $api->openOrders("BNBBTC");
        print_r($openorders);




		$data = json_decode(file_get_contents("https://api.trx.market/api/exchange/user/order?start=0&limit=50&uAddr=".$account->data['trx_address']."&status=0"), true)['data']['rows'];
		
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
		}
	}

	//TODO:обсудить/переделать
	//public static function cancelOrder($account, $external_id) {
	public static function cancelOrder($account, Order $order) {

	    if($account->type==Account::BINANCE)
	        return 0;//это не аккаунт бинанса
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_one=$order->getMain_currency()->one();
        $currency_two=$order->getSecond_currency()->one();

        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $response = $api->cancel("ETHBTC", $order->market_order_id);
	    return $response;
	}
}


?>