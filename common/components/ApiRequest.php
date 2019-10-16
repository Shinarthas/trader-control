<?
namespace common\components;

class ApiRequest
{
	public static function accounts($action, $data, $proxy = false) {
		return self::request('accounts', $action, $data);
	}

	public static function control($action, $data, $proxy = false) {
		return self::request('control', $action, $data);
	}
	
	public static function statistics($action, $data, $proxy = false) {
		return self::request('statistics', $action, $data);
	}
	
	public static function request($server,$action, $data, $proxy = false) {

		if($server == 'accounts')
			$url = \Yii::$app->params['account-api-url'];
		elseif($server == 'control')
			$url = \Yii::$app->params['control-api-url'];
		elseif($server == 'statistics')
			$url = \Yii::$app->params['statistics-api-url'];
		
		$data['key'] = md5('xc;nj;235[xznhc09[3,v62398mnp:IUNPOnuh023v%*#JVM%8mj2342610_N)*(Hsdnh'.date("Y-m-d",time()));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'/'.$action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($proxy != false)
			curl_setopt($ch, CURLOPT_PROXY, $proxy);
			
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		$res = curl_exec($ch);
		
		return json_decode($res);
	}
}

?>