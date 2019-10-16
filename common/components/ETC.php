<?
namespace common\components;
use common\models\ETCError;
use common\models\Proxy;

class ETC
{
	public static function request($params, $server_url = false, $proxy = false)
	{
//echo 'req ';
		$data = $params;
		
		$data = json_decode($data, true);
		if(!isset($data['owner_address']))
			$data['owner_address'] = '4138E3E3A163163DB1F6CFCECA1D1C64594DD1F0CA';
			
		$data = json_encode($data);
		
		//echo json_encode($data);exit();
		$ch = curl_init();
		
		if(!$server_url)
			curl_setopt($ch, CURLOPT_URL, "https://api.trongrid.io/wallet/triggersmartcontract");
		else
			curl_setopt($ch, CURLOPT_URL, $server_url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($proxy != false)
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			
		//echo $proxy."\r\n";
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$res = curl_exec($ch);
		
		if($res !== FALSE)
		{
			$formatted = json_decode($res, true);
			
			if(isset($formatted->error))
			{
				return $formatted;
			}
			else
			{
				return $formatted;
			}
		}
		else
		{
			return "Server did not respond";
		}
	
	}
	
	public static function triggerContract($contract, $value, $function, $parameters, $account, $proxy = false, $repeats = 2) {
		
		$account['address'] = strtolower($account['address']);
		$account['key'] = strtolower($account['key']);
		
		
		for($i=1;$i<=$repeats;$i++)
		{
			$start_time = time();
			$contract = strtolower($contract);
			$error = new ETCError;
			$error->created_at = time();
			$error->input = [ 'contract_address' => $contract,
				'function_selector' => $function,
				'parameter' => $parameters,
				'owner_address' =>  $account['address'],
				'call_value'    =>  $value,
				'proxy' => $proxy
				];
			
			$parameters_string = '';
			
			foreach($parameters as $p)
				$parameters_string.= $p;
		
			$transaction = [];
			$transaction = ETC::request(json_encode([
				'contract_address' => $contract,
				'function_selector' => $function,
				'parameter' => $parameters_string,
				'owner_address' =>  $account['address'],
				'fee_limit'     =>  1000000000,
				'call_value'    =>  $value,
				'consume_user_resource_percent' =>  50 ]), false, $proxy);
				
			if(time()-$start_time > 28)
			{
				if($proxy != false AND $i!=$repeats)
					$proxy = Proxy::top();
					
				continue;
			}
			
			if(!isset($transaction['transaction']))
			{
				$error->output = $transaction;
				$error->info = ['status'=>'error on step 1'];
				//$error->save();
				
				if($proxy != false AND $i!=$repeats)
					$proxy = Proxy::top();
					
				continue;
			}
			
			$signed_transaction = [];
			$data = ['transaction'=>$transaction['transaction'], 'privateKey'=> $account['key']];
			$signed_transaction = ETC::signTransaction($data, $start_time);
			
			if(!isset($signed_transaction['txID']))
			{
				$error->output = ['transaction'=>$transaction['transaction'], 'signature'=> $account['key'], 'answer'=>$signed_transaction];
				$error->info = ['status'=>'error on sign'];
				
				//$error->save();
				
				if($proxy != false AND $i!=$repeats)
					$proxy = Proxy::top();
					
				continue;
			}
				
			$result = [];
			
			if(time()-$start_time >30)
				$proxy = false;
			if(time()-$start_time >55)
				continue;
				
			$result = ETC::request(json_encode($signed_transaction), 'https://api.trongrid.io/wallet/broadcasttransaction', $proxy);
			
			if(!isset($result['result']))
			{
				$error->output = $signed_transaction;
				$error->info = ['status'=>'error on checking signature', 'result'=>$result];
				//$error->save();
				
				if($proxy != false AND $i!=$repeats)
					$proxy = Proxy::top();
				
				continue;
			}
			
			if($result['result']==1)
				return true;
				
			return false;
			
		}
		
		return false;
		//print_r($result);
		//exit();
	}
	
	public static function signTransaction($data, $start_time) {
	
		$data_json_ETC = str_replace('\\','',json_encode($data));
		$data['signature'] = $data['privateKey'];
		unset($data['privateKey']);
		
		$data_json_nodejs = str_replace('\\','',json_encode($data));
		
		$opts = ['http' => [
				'method'  => 'POST',
				'header'  => 'Content-type: application/x-www-form-urlencoded',
				'content' => $data_json_nodejs ] ];

		$ip = '51.15.140.249';
		
		if(isset(\Yii::$app->params['node_js_ip'])){
			$ip = \Yii::$app->params['node_js_ip'];
		}
		$signed_transaction = json_decode(file_get_contents('http://'.$ip.':3333/key_check', false,  stream_context_create($opts)), true);
		
		if(time()-$start_time >45)
			return false;
				
		if(!isset($signed_transaction['txID']))
			$signed_transaction = json_decode(file_get_contents('http://'.$ip.':3333/key_check', false,  stream_context_create($opts)), true);
			
		if(time()-$start_time >45)
			return false;
		
		if(!isset($signed_transaction['txID']))
			$signed_transaction = ETC::request($data_json_ETC, 'https://api.trongrid.io/wallet/gettransactionsign');
		
		return $signed_transaction;
	}
	
	public static function getProxy() {
		return '66.96.228.97:30564';
	}
	
	
	public static function sendPost($url,$data)
	{
		$context = stream_context_create(['http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'content' => http_build_query($data)
			]]);
			
		return file_get_contents($url, false, $context);
	}
	
	

	public static function fromHex($string)
    {
        if(strlen($string) == 42 && mb_substr($string,0,2) === '41') {
            return ETC::hexString2Address($string);
        }
        return ETC::hexString2Utf8($string);
    }

    public static function toHex($str)
    {
        if(mb_strlen($str) == 34 && mb_substr($str, 0, 1) === 'T') {
            return ETC::address2HexString($str);
        };
        return ETC::stringUtf8toHex($str);
    }

    public static function address2HexString($sHexAddress)
    {
        if(strlen($sHexAddress) == 42 && mb_strpos($sHexAddress, '41') == 0) {
            return $sHexAddress;
        }
        return Base58Check::decode($sHexAddress,0,3);
    }

    public static function hexString2Address($sHexString)
    {
        if(!ctype_xdigit($sHexString)) {
            return $sHexString;
        }
        if(strlen($sHexString) < 2 || (strlen($sHexString) & 1) != 0) {
            return '';
        }
        return Base58Check::encode($sHexString,0,false);
    }

    public static function stringUtf8toHex($sUtf8)
    {
        return bin2hex($sUtf8);
    }

    public static function hexString2Utf8($sHexString)
    {
        return hex2bin($sHexString);
    }

	public static function events($name, $timestamp, $smart_contract = false) {
	echo 'ev ';

		if(!$smart_contract)
			$smart_contract = \Yii::$app->params['mainContract'];
			
		
		
		//echo json_encode($data);exit();
		$ch = curl_init();
		//echo 'https://api.trongrid.io/event/contract/'.$smart_contract.'/'.$name.'?since='.$timestamp.'000';
		curl_setopt($ch, CURLOPT_URL, 'https://api.trongrid.io/event/contract/'.$smart_contract.'/'.$name);

		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if(false)
		{
			//echo 'proxyev ';
			$proxy = ETC::getProxy(); 
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
		}	

		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json')); 

		$res = curl_exec($ch);
		
		curl_close($ch); 

		$out = [];
		foreach(json_decode($res, true) as $info) {
			$out[] = $info;
		}
		
		return $out;
	}
	
	public static function decTo64bitHex($dec) {
	
		$sec_string = self::bcdechex($dec)."";
		
		$strlen = strlen($sec_string);
		
		for($i=0;$i < (64 - $strlen); $i++ )
			$sec_string = "0".$sec_string;
			
		return $sec_string;
	}
	
	public static function hexTo64bitHex($hex) {
		$strlen = strlen($hex);
		
		for($i=0;$i < (64 - $strlen); $i++ )
			$hex = "0".$hex;
			
		return $hex;
	}
	
	public static function bcdechex($dec) {
		$hex = '';
		do {    
			$last = bcmod($dec, 16);
			$hex = dechex($last).$hex;
			$dec = bcdiv(bcsub($dec, $last), 16);
		} while($dec>0);
		return $hex;
	}
}

?>