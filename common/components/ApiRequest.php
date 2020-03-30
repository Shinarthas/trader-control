<?
namespace common\components;

use common\models\Log;

class ApiRequest
{
	public static function accounts($action, $data, $proxy = false) {
		return self::request('accounts', $action, $data);
	}

	public static function control($action, $data, $proxy = false) {
		return self::request('control', $action, $data);
	}
	
	public static function statistics($action, $data=[], $proxy = false) {
		return self::request('statistics', $action, $data);
	}
    public static function analyzer($action, $data=[], $proxy = false) {
        return self::request('analyzer', $action, $data);
    }
	
	public static function request($server,$action, $data, $proxy = false) {

		if($server == 'accounts')
			$url = \Yii::$app->params['account-api-url'];
		elseif($server == 'control')
			$url = \Yii::$app->params['control-api-url'];
		elseif($server == 'statistics')
			$url = \Yii::$app->params['statistics-api-url'];
        elseif($server == 'analyzer')
            $url = \Yii::$app->params['analyzer-api-url'];
		
		$data['key'] = md5('xc;nj;235[xznhc09[3,v62398mnp:IUNPOnuh023v%*#JVM%8mj2342610_N)*(Hsdnh'.date("Y-m-d",time()));

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url.'/'.$action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if($proxy != false)
			curl_setopt($ch, CURLOPT_PROXY, $proxy);

		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		
		$res = curl_exec($ch);
		$response=json_decode($res);
		if(strpos($action,'/log')===false && !isset($response->status)){
		    Log::log($response,false, 'ApiRequest');
        }

		return json_decode($res);
	}
}

?>