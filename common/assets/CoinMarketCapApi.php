<?php


namespace common\assets;


use common\models\Currency;
use CoinMarketCap;
use common\models\GlobalCurrency;

class CoinMarketCapApi
{
    public static function top(){
        $cmc = new CoinMarketCap\Api(\Yii::$app->params['coinmarketcap-key']);
        $response = $cmc->cryptocurrency()->listingsLatest(['limit' => 100, 'convert' => 'USD','sort'=>'percent_change_1h']);
        return $response;
    }
    public static function info($cmc_currency_id){
        $url="https://web-api.coinmarketcap.com/v1/cryptocurrency/market-pairs/latest?aux=num_market_pairs,category,fee_type,market_url,notice,price_quote,effective_liquidity&convert=BTC&id=$cmc_currency_id&limit=400&sort=cmc_rank";
        $data=file_get_contents($url);
        return json_decode($data);
    }

    public static function listAll(){
        $cmc = new CoinMarketCap\Api(\Yii::$app->params['coinmarketcap-key']);
        $response = $cmc->cryptocurrency()->map(['limit' => 10]);
        foreach ($response->data as $c){
            $currency=GlobalCurrency::find()->where(['symbol'=>$c->symbol])->limit(1)->one();
            if(empty($currency)){
                $currency=new GlobalCurrency();
                $currency->created_at=date('Y-m-d H:i:s',time());
            }
            $currency->name=$c->name;
            $currency->symbol=$c->symbol;
            $currency->is_active=$c->is_active;
            $currency->updated_at=date('Y-m-d H:i:s',time());

            $currency->save();
        }
        //print_r($response);
    }
    public static function getAllExchanges(){
        $ch = curl_init();
        $params=http_build_query([
            'key'=>\Yii::$app->params['nomics-key'],
            'start'=>1,
            'limit'=>10,

        ]);
        $url="https://api.nomics.com/v1/exchanges/ticker?".$params;
        curl_setopt($ch, CURLOPT_URL,$url);
        echo $url."\n\n";
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS,$vars);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'X-CMC_PRO_API_KEY: '.\Yii::$app->params['coinmarketcap-key'],
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output = curl_exec ($ch);

        curl_close ($ch);

        print  $server_output ;
    }
}