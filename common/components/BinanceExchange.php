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
        $currency_pair=$currency_two->symbol.$currency_one->symbol;

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
        $currency_pair=$currency_two->symbol.$currency_one->symbol;

        //TODO:support proxy
        $order = $api->buy($currency_pair, $tokens_count, $price);
        return $order;
	}
	
	public static function exchangeRates($currency_one, $currency_two) {
		$api = new Binance\API();
        //TODO: �������� �� ������ �� �� � �������� �� ������
        //$currency_pair=$currency_two->symbol.$currency_one->symbol;
        $currency_pair=$currency_two->symbol.$currency_one->symbol;//  we have to keep same structure all over
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
        $currency_pair=$currency_two->symbol.$currency_one->symbol;
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

        $currency_pair=$currency_two->symbol.$currency_one->symbol;
        $response = $api->cancel("ETHBTC", $order->market_order_id);
	    return $response;
	}
}


?>