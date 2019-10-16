<?
namespace common\components;
use common\models\Task;
use common\models\AccountBalance;
use common\models\Currency;
use common\models\RateHistory;
use common\models\TaskAdditional;

class MyTronExchange {
	
	const CONTRACT_ADDRESS = '414DE57E6406BAEFEFF3440BFC4EB711B22D644499';
	
	// текущий курс продажи и покупки?
	public static function sellOrder($currency_one, $currency_two, $tokens_count, $price, $account) {
		
		// approve tokens
		$parameters = [
			ETC::hexTo64bitHex(substr(self::CONTRACT_ADDRESS,2)),
			ETC::decTo64bitHex($tokens_count* 10**$currency_two->decimals)
		];

		$result = ETC::triggerContract($currency_two->address, 0, 'approve(address,uint256)', $parameters, $account->data, $account->proxy);
		
		if(!$result)
			return false;
		
		$rate = $price* 10**$currency_one->decimals;
		$value = $rate*$tokens_count;
		
		$function = 'sellOrder(address,uint256,address,uint256,uint256)';
		$parameters = [
			ETC::hexTo64bitHex($currency_two->address),
			ETC::decTo64bitHex($tokens_count* 10**$currency_two->decimals),
			ETC::decTo64bitHex(0), 
			ETC::decTo64bitHex($value), 
			ETC::decTo64bitHex($rate)
		];
		
		return ETC::triggerContract(self::CONTRACT_ADDRESS, 0, $function, $parameters, $account->data, $account->proxy);
	}
	
	// текущий курс продажи и покупки?
	public static function buyOrder($currency_one, $currency_two, $tokens_count, $price, $account) {

		$rate = $price* 10**$currency_one->decimals;
		$value = $rate*$tokens_count;
		
		$function = 'buyOrder(address,uint256,address,uint256,uint256)';
		$parameters = [
			ETC::hexTo64bitHex($currency_two->address),
			ETC::decTo64bitHex($tokens_count* 10**$currency_two->decimals),
			ETC::decTo64bitHex(0), 
			ETC::decTo64bitHex($value), 
			ETC::decTo64bitHex($rate)
		];
		
		//$proxy = false;
		//if(rand(0,1)==1)
			$proxy = $account->proxy;
		return ETC::triggerContract(self::CONTRACT_ADDRESS, $value, $function, $parameters, $account->data, $proxy);
	}
	
	public static function exchangeRates($currency_one, $currency_two) {
		

		
		return [
			'buy_price' => 25,
			'sell_price' => 26
		];
	}
	
	public static function loadUncompletedOrders($promotion, $account) {
		return true;
	}
	
	public static function cancelOrder($account, $task) {
		return true;
	}
}


?>