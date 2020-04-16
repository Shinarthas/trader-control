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
use common\assets\BinanceApi;
class BinanceExchange {
	
	const CONTRACT_ADDRESS = '41b3bddae866b2ce2349bdcb59dfbfa1a75f8552da';

	const BINANCE=3;
	// ������� ���� ������� � �������?
	public static function sellOrder($currency_one, $currency_two, $tokens_count, $price, $account) {
		if($account->type!=self::BINANCE)
		    return 0;

		//$proxy = $account->proxy;
        $api = new Binance\API($account->data['api_key'],$account->data['secret']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        //TODO:support proxy
        $order = $api->sell($currency_pair, $tokens_count, $price);
        return $order;
	}
	
	// ������� ���� ������� � �������?
	public static function buyOrder($currency_one, $currency_two, $tokens_count, $price, $account) {

        if($account->type!=self::BINANCE)
            return 0;

        //$proxy = $account->proxy;
        $api = new Binance\API($account->data['api_key'],$account->data['secret']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;

        //TODO:support proxy
        $order = $api->buy($currency_pair, $tokens_count, $price);
        return $order;
	}
	
	public static function exchangeRates($currency_one, $currency_two) {
		$api = new Binance\API();
        //TODO: �������� �� ������ �� �� � �������� �� ������
        //$currency_pair=$currency_one->symbol.$currency_two->symbol;
        $currency_pair=$currency_one->symbol.$currency_two->symbol;//  we have to keep same structure all over
        error_reporting(0);//disable notice for binance api
        $depth = $api->depth($currency_pair);
        return [
            'buy_price' => array_key_first($depth['asks']),
            'sell_price' => array_key_first($depth['bids'])
        ];
	}
	
	public static function loadUncompletedOrders($promotion, $account) {
        if($account->type==Account::BINANCE)
            return 0;//��� �� ������� �������
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_one=Currency::findOne($promotion['currency_one']);
        $currency_two=Currency::findOne($promotion['currency_two']);
        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $openorders = $api->openOrders();
        //print_r($openorders);
        return $openorders;

	}

	//TODO:��������/����������
	//public static function cancelOrder($account, $external_id) {
	public static function cancelOrder($account, Order $order) {

	    if($account->type==Account::BINANCE)
	        return 0;//��� �� ������� �������
        $api = new Binance\API($account->data->api_key,$account->data->secret);
        $currency_one=$order->getMain_currency()->one();
        $currency_two=$order->getSecond_currency()->one();

        $currency_pair=$currency_one->symbol.$currency_two->symbol;
        $response = $api->cancel("ETHBTC", $order->market_order_id);
	    return $response;
	}
	
	public static function getDepth($currency_one, $currency_two) {

	    $api = new BinanceApi();
		$cache = \Yii::$app->cache;
		$currency_id = $cache->get("currency_id_".$currency_two->symbol.$currency_one->symbol);
		if($currency_id == 0)
			$cache->set("currency_id_".$currency_two->symbol.$currency_one->symbol, $currency_two->id);
			
		$api->depthCache([$currency_two->symbol.$currency_one->symbol], function($api, $symbol, $depth) {

				$enter_at = 67;
				$enter_previous_at = 55;
				$exit_at = 50;

				$cache = \Yii::$app->cache;
				$current_currency_to_buy = $cache->get("current_currency_to_buy");
				$currency_id = $cache->get("currency_id_".$symbol);
				
				$last_prediction = $cache->get("statistic_".$symbol);
				$last_percent_prediction = $last_prediction['prediction'];
				
				if($current_currency_to_buy != $currency_id AND $current_currency_to_buy!=null)
					return false;
				
				$prediction = Diviner::depthPrediction($depth['asks'], $depth['bids'], $symbol, $currency_id);

				$limit = 11;
				$sorted = $api->sortDepth($symbol, $limit);
				
				// debug 
				echo $prediction['prediction'].' '. (int)$current_currency_to_buy . ' '.$last_percent_prediction."\r\n";
				
				if($prediction['prediction'] > $enter_at AND $current_currency_to_buy == 0 AND $last_percent_prediction > $enter_previous_at) {
					// store currenct currency as currency in buy
					$cache->set("current_currency_to_buy", $currency_id);
										
					// purchase currency by id $currency_id;
					// code of purchs must be here
				}
				
				if($prediction['prediction'] < $exit_at AND $current_currency_to_buy == $currency_id) {
					// sell currency by id $currency_id
					// code of sell must be here
					
					// and after this - clear cache data
					$cache->set("current_currency_to_buy", 0);
				}
				
				if(date("s",time())==59) {
					$endpoint = strtolower( $symbol ) . '@depthCache';
					$api->terminate( $endpoint );
				}
		});
	}
}


?>