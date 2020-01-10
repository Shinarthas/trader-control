<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use backend\assets\DepthAnalizer;
use common\assets\BikiApi;
use common\assets\CoinMarketCapApi;
use common\components\ApiRequest;
use common\models\Account;
use common\models\Campaign;
use common\models\CurrencyPrice;
use common\models\DemoTask;
use common\models\Trading;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Task;
use common\models\Promotion;

class ScheduleController extends Controller
{
    public static $pairs=["ETHBTC","LINKBTC","TRXBTC","BNBBTC","XRPBTC","LTCBTC","VETBTC","EOSBTC","LSKBTC","XMRBTC"];
    public static $btc_usdt_rate=[];
	public function actionAddOrders($date='ololo'){
	    //die();
        echo " start ";
        //get praphs
        if($date=='ololo')
            $date_end=date("Y-m-d 00:00:00",time());
        else
            $date_end=$date;

        $date_start=date("Y-m-d H:i:s",strtotime($date_end)-1*24*3600);
        echo " bart ";
        $btc_usdt=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>$date_start,'date_end'=>$date_end]);
        $btc_usdt=json_decode(json_encode($btc_usdt->data),true);
        self::$btc_usdt_rate=$btc_usdt;
        //print_r($btc_usdt);
        //die();
        $percent_drop=0.004;
        $percent_bounce=0.001;
        $percent_profit=0.01;
        $timeout=4*3600;
        $gaps=[1,2,3,4,6,8,12,24];
        //получем каждую валюту
        echo " loop ";
        foreach (self::$pairs as $symbol){
            echo "a";
            $data=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>$symbol,'date_start'=>$date_start,'date_end'=>$date_end]);
            $data=json_decode(json_encode($data->data),true);
            $totalData=[];
            $gData=[];
            foreach ($data as $key=>$value) {
                $gData[]=["x"=>$key/1000,"y"=>$value['open']];
            }
            for ($i=0;$i<count($gData)-$timeout/600-1;$i++){
                if(count($totalData)>100*10)
                    break;
                $bid_now=$gData[$i]['y'];
                for ($j=0;$j<count($gaps);$j++){
                    if (!isset( $gData[$i - $j]))
                        continue;
                    $time1=$gData[$i-$j]['y'];
                    for($k=$j;$k<count($gaps);$k++) {
                        if (!isset($gData[$i - $j - $k]))
                            continue;
                        $time2=$gData[$i-$j-$k]['y'];
                        if(($time2-$time1)/$time1>$percent_drop && ($bid_now-$time1)/$time1>$percent_bounce){
                            $strategyStart=[];
                            //тут показываем как зашли
                            for ($ii=$i-$j-$k; $ii<$i;$ii++){
                                $strategyStart[]=$gData[$ii];
                            }
                            $totalData[]=[
                                "color"=>'#cccccc',
                                "type"=>"line",
                                "dataPoints"=> $strategyStart
                            ];

                            //тут показываем как играли и создаем ордер
                            $value_start=$gData[$i]['y'];//по какому курсу зашли
                            $value_current=$gData[$i]['y'];//это какой курс сейчас, в момент интерации
                            $strategyProcess=[];
                            $ending_point=$i;
                            $ii_global=0;//мне это нужно знать, чтобы сказать когдая вышел из ставки, тут возможен баг

                            //когда  ставки игралась еще и еще но мы не отображаем это
                            for ($ii=$i;$ii<count($gData)-1;$ii++){// так как графики 5 мин, то разделим
                                if($ii>$i+$timeout/600)
                                    break;
                                $ii_global=$ii;
                                $ending_point=$ii;

                                $value_current=$gData[$ii]['y'];
                                $strategyProcess[]=$gData[$ii];
                                if($value_current>$value_start*(1+$percent_profit))
                                    break;//типо сработала ставка
                            }
                            //выберем цвет для графика в зависимосит от того выиграли  или проиграли
                            if($value_current>$value_start){
                                $color='#4fcc37';
                            }else{
                                $color='#fb564c';
                            }
                            $totalData[]=[
                                'color'=>$color,
                                'type'=>'line',
                                'dataPoints'=>$strategyProcess
                            ];

                            $order=[];

                            $order['pair']=$symbol;
                            $order['date_start']=$gData[$i]['x'];
                            $order['date_end']=$gData[$ii_global]['x'];
                            $order['rate_start']=$value_start;
                            $order['rate_end']=$value_current;
                            $order['usdt_bank']=100000;
                            $order['profit']=(100000/$value_start*$value_current)-100000;
                            $i=$ii_global;
                            $this->addTrade($order);
                            echo  'die1';
                            //die();
                        }
                    }
                }
            }
            echo  'die2';
            //die();
        }
    }

    public function addTrade($order){
	    $currency_one=str_replace('BTC','',$order['pair']);
	    $currency_two='BTC';

	    //найдем есть ли такой ордер
        $is_demo_order1=DemoTask::find()->where(['currency_one'=>$currency_one,'currency_two'=>$currency_two])
            ->andWhere(['sell'=>0,'rate'=>$order['rate_start']])->andWhere(['time'=>$order['date_start']])
            ->limit(1)->one();
        $is_demo_order2=DemoTask::find()->where(['currency_one'=>$currency_one,'currency_two'=>$currency_two])
            ->andWhere(['sell'=>1,'rate'=>$order['rate_end']])->andWhere(['time'=>$order['date_start']])
            ->limit(1)->one();
        //ничегоне делаем если есть хоть  1 совпадение
        if(!empty($is_demo_order1) || !empty($is_demo_order2)){
            echo " empty ";
            return;
        }

        //типо проигрышный
        if(floatval($order['rate_start'])>floatval($order['rate_end'])){
            echo " lose ";
            return;
        }


        if(isset(self::$btc_usdt_rate[$order['date_start']*1000]))
            $tokens_count=100000/(self::$btc_usdt_rate[$order['date_start']*1000]['open'])/$order['rate_start'];
        else{
            foreach (self::$btc_usdt_rate as $key1=>$val1){
                $rateBtc=$val1['open'];
                break;
            }
            $tokens_count=100000/$rateBtc/$order['rate_start'];
        }
        if($tokens_count<1){
            echo $order['rate_start'];
            if(isset($rateBtc))
                echo " $rateBtc ";

            if(isset(self::$btc_usdt_rate[$order['date_start']*1000]))
                print_r(self::$btc_usdt_rate[$order['date_start']*1000]);
        }

        $buy_order=new DemoTask();
        $buy_order->company_id=1;
        $buy_order->status=5;
        $buy_order->sell=0;
        $buy_order->tokens_count=$tokens_count;// ЧТО ТУТ ДЕЛАТЬ?
        $buy_order->rate=$order['rate_start'];
        $buy_order->progress=100;
        $buy_order->data_json="{'asd':'asd'}";
        $buy_order->external_id=1;
        $buy_order->time=$order['date_start'];
        $buy_order->created_at=$order['date_start'];
        $buy_order->loaded_at=$order['date_start'];
        $buy_order->currency_one=$currency_one;
        $buy_order->currency_two=$currency_two;
        $tmp=(int)$order['date_start']/(6*3600);
        $buy_order->period=$tmp*6*3600;
        $buy_order->save();

        $sell_oorder=new DemoTask();
        $sell_oorder->company_id=1;
        $sell_oorder->status=5;
        $sell_oorder->sell=1;
        $sell_oorder->tokens_count=$tokens_count;// ЧТО ТУТ ДЕЛАТЬ?
        $sell_oorder->rate=$order['rate_end'];
        $sell_oorder->progress=100;
        $sell_oorder->data_json="{'asd':'asd'}";
        $sell_oorder->external_id=1;
        $sell_oorder->time=$order['date_start'];
        $sell_oorder->created_at=$order['date_start'];
        $sell_oorder->loaded_at=$order['date_end'];
        $sell_oorder->currency_one=$currency_one;
        $sell_oorder->currency_two=$currency_two;
        $tmp=(int)$order['date_start']/(6*3600);
        $sell_oorder->period=$tmp*6*3600;
        $sell_oorder->save();
    }
}