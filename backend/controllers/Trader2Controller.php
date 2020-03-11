<?php
namespace backend\controllers;

use Codeception\Template\Api;
use common\assets\CoinMarketCapApi;
use common\assets\Hitbtc\Model\Order;
use common\components\ApiRequest;
use common\models\Campaign;
use common\models\Company;
use common\models\Currency;
use common\models\DemoBalance;
use common\models\DemoTask;
use common\models\Market;
use common\models\Trading;
use Fpdf\Fpdf;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Account;
use common\models\Promotion;
use common\models\Task;
use common\models\AccountBalance;
use common\models\CurrencyPrice;
use common\models\ETCError;
use common\models\PromotionAccount;

class Trader2Controller extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function actionIndex(){
	    $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'limit'=>10]);
        $trading_pairs=$trading_pairs->data;
        $period=Trading::getPeriod();


        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp;
        }

        $trading_pairs_remapped=[];
        foreach ($trading_pairs as $trading_pair){
            $trading_pairs_remapped[$trading_pair->trading_paid]=$trading_pair;
        }
        //фунуионал статистики по ордерам
        $time=time();
        $orders=$tasks=Task::find()->where(['>','time',time()-7*24*3600])->andWhere(['<>','status',1])->all();

        $order_statistics=[
            'hour'=>[
                'total'=>0,
                'failed'=>0,
                'open'=>0,
                'completed'=>0,
                'canceled'=>0,
                'buy'=>0,
                'sell'=>0,
                'human'=>0,
                'bot'=>0,
            ],
            'day'=>[
                'total'=>0,
                'failed'=>0,
                'open'=>0,
                'completed'=>0,
                'canceled'=>0,
                'buy'=>0,
                'sell'=>0,
                'human'=>0,
                'bot'=>0,
            ],
            'week'=>[
                'total'=>0,
                'failed'=>0,
                'open'=>0,
                'completed'=>0,
                'canceled'=>0,
                'buy'=>0,
                'sell'=>0,
                'human'=>0,
                'bot'=>0,
            ]
        ];
        foreach ($tasks as $t){
            if($t->time>$time-3600){
                $order_statistics['hour']['total']+=1;
                if($t->status==Task::STATUS_STARTED) $order_statistics['hour']['failed']++;
                if($t->status==Task::STATUS_CREATED) $order_statistics['hour']['open']++;
                if($t->status==Task::STATUS_COMPLETED) $order_statistics['hour']['completed']++;
                if($t->status==Task::STATUS_CANCELED) $order_statistics['hour']['canceled']++;
                if($t->sell) $order_statistics['hour']['sell']++;
                else $order_statistics['hour']['buy']++;

                if($t->is_user) $order_statistics['hour']['human']++;
                else $order_statistics['hour']['bot']++;
            }
            if($t->time>$time-24*3600){
                $order_statistics['day']['total']+=1;
                if($t->status==Task::STATUS_STARTED) $order_statistics['day']['failed']++;
                if($t->status==Task::STATUS_CREATED) $order_statistics['day']['open']++;
                if($t->status==Task::STATUS_COMPLETED) $order_statistics['day']['completed']++;
                if($t->status==Task::STATUS_CANCELED) $order_statistics['day']['canceled']++;
                if($t->sell) $order_statistics['day']['sell']++;
                else $order_statistics['day']['buy']++;

                if($t->is_user) $order_statistics['day']['human']++;
                else $order_statistics['day']['bot']++;
            }
            if($t->time>$time-7*24*3600){
                $order_statistics['week']['total']+=1;
                if($t->status==Task::STATUS_STARTED) $order_statistics['week']['failed']++;
                if($t->status==Task::STATUS_CREATED) $order_statistics['week']['open']++;
                if($t->status==Task::STATUS_COMPLETED) $order_statistics['week']['completed']++;
                if($t->status==Task::STATUS_CANCELED) $order_statistics['week']['canceled']++;
                if($t->sell) $order_statistics['week']['sell']++;
                else $order_statistics['week']['buy']++;

                if($t->is_user) $order_statistics['week']['human']++;
                else $order_statistics['week']['bot']++;
            }
        }

        $balance_statistics_data=ApiRequest::statistics('v1/account/balances-per-time',[
            'intervals'=>[$time-24*3600*7,$time-24*3600,$time-3600,$time]
        ]);
        $balance_statistics=[
            'week'=>$balance_statistics_data->data->{$time-24*3600*7},
            'day'=>$balance_statistics_data->data->{$time-24*3600},
            'hour'=>$balance_statistics_data->data->{$time-3600},
            'now'=>$balance_statistics_data->data->{$time},
        ];

        $trading_pairs2=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'limit'=>10]);
        $trading_pairs2=$trading_pairs2->data;


        foreach ($trading_pairs2 as $trading_pair2){
            $data1=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>$trading_pair2->trading_paid,'date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999]);
            $trading_pair2->statistics=$data1->data;
        }

        $trading_pairs_remapped=[];
        foreach ($trading_pairs2 as $trading_pair2){
            //$trading_pairs_remapped[str_replace('BTC','USDT',$trading_pair2->trading_paid)]=json_decode(json_encode(['ass'=>'ass']));
            //$trading_pairs_remapped[str_replace('BTC','USDT',$trading_pair2->trading_paid)]->statistics=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>str_replace('BTC','USDT',$trading_pair2->trading_paid),'date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999])->data;


            $trading_pairs_remapped[$trading_pair2->trading_paid]=$trading_pair2;
        }



        $trading_pairs_remapped['BTCUSDT']=json_decode(json_encode(['ass'=>'ass']));
        $trading_pairs_remapped['BTCUSDT']->statistics=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999])->data;

        $profit_statistics=[
            'week'=>['total_orders'=>0,'profitable_orders'=>0,'profit'=>0],
            'day'=>['total_orders'=>0,'profitable_orders'=>0,'profit'=>0],
            'hour'=>['total_orders'=>0,'profitable_orders'=>0,'profit'=>0]
        ];

        foreach ($orders as $order){
            if($order->status==Task::STATUS_COMPLETED) {
                $profit = 0;
                if ($order->start_rate) {
                    if ($order->sell)
                        $profit = $order->rate / $order->start_rate * 100 - 100;
                    else
                        $profit = $order->start_rate / $order->rate * 100 - 100;


                    if ($order->time > $time - 3600) {
                        $profit_statistics['hour']['total_orders'] += 1;
                        $profit_statistics['hour']['profit'] += $profit;
                        $profit_statistics['hour']['profitable_orders'] += $profit > 0 ? 1 : 0;
                    }
                    if ($order->time > $time - 24 * 3600) {
                        $profit_statistics['day']['total_orders'] += 1;
                        $profit_statistics['day']['profit'] += $profit;
                        $profit_statistics['day']['profitable_orders'] += $profit > 0 ? 1 : 0;

                    }
                    if ($order->time > $time - 7 * 24 * 3600) {
                        $profit_statistics['week']['total_orders'] += 1;
                        $profit_statistics['week']['profit'] += $profit;
                        $profit_statistics['week']['profitable_orders'] += $profit > 0 ? 1 : 0;
                    }
                }else{

                    foreach ($trading_pairs_remapped[$order->currency_one.$order->currency_two]->statistics as $time_milliseconds=>$stat){
                        if(abs($order->time-$time_milliseconds)>350*1000 && abs($order->time-$time_milliseconds)>900*1000){
                            echo 'a';
                            if ($order->sell)
                                $profit = $order->rate / $stat->open * 100 - 100;
                            else
                                $profit = $stat->open / $order->rate * 100 - 100;


                            if ($order->time > $time - 3600) {
                                $profit_statistics['hour']['total_orders'] += 1;
                                $profit_statistics['hour']['profit'] += $profit;
                                $profit_statistics['hour']['profitable_orders'] += $profit > 0 ? 1 : 0;
                            }
                            if ($order->time > $time - 24 * 3600) {
                                $profit_statistics['day']['total_orders'] += 1;
                                $profit_statistics['day']['profit'] += $profit;
                                $profit_statistics['day']['profitable_orders'] += $profit > 0 ? 1 : 0;

                            }
                            if ($order->time > $time - 7 * 24 * 3600) {
                                $profit_statistics['week']['total_orders'] += 1;
                                $profit_statistics['week']['profit'] += $profit;
                                $profit_statistics['week']['profitable_orders'] += $profit > 0 ? 1 : 0;
                            }

                            break;
                        }

                    }

                }
            }

        }


	   $Companies=Campaign::find()->all();
        return $this->render("index", [
            'companies' => $Companies,
            'trading_pairs'=>$trading_pairs,
            'orders'=>$orders,
            'period'=>$period,
            'trading_pairs_remapped'=>$trading_pairs_remapped,
            'order_statistics'=>$order_statistics,
            'balance_statistics'=>$balance_statistics,
            'profit_statistics'=>$profit_statistics,
        ]);
    }

    public function actionOrders($id){
	    $get=Yii::$app->request->get();
	    if(isset($get['date_start']))
	        $date_start=date('Y-m-d',strtotime($get['date_start']));
	    else
	        $date_start=date('Y-m-d',time());

        $trading_pairs=ApiRequest::statistics('v1/trader2/list',[]);
        $trading_pairs=$trading_pairs->data;


        $orders=Task::find()
            //->where(['>=','time',strtotime($date_start)+2*3600])->andWhere(['<','time',strtotime($date_start)+24*3600+2*3600])
            ->andWhere(['not in','status',[1,3]])
            ->andWhere(['campaign_id'=>$id])
            ->orderBy([
            'time' => SORT_DESC,
            'id'=>SORT_DESC
        ])->limit(400)
            //->createCommand()->rawSql;
            ->all();

        $trading_pairs2=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'limit'=>10]);
        $trading_pairs2=$trading_pairs2->data;


        foreach ($trading_pairs2 as $trading_pair2){
            $data1=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>$trading_pair2->trading_paid,'date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999]);
            $trading_pair2->statistics=$data1->data;
        }
        $trading_pairs_remapped=[];
        foreach ($trading_pairs2 as $trading_pair2){
            $trading_pairs_remapped[str_replace('BTC','USDT',$trading_pair2->trading_paid)]=json_decode(json_encode(['ass'=>'ass']));
            $trading_pairs_remapped[str_replace('BTC','USDT',$trading_pair2->trading_paid)]->statistics=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>str_replace('BTC','USDT',$trading_pair2->trading_paid),'date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999])->data;


            $trading_pairs_remapped[$trading_pair2->trading_paid]=$trading_pair2;
        }
        $trading_pairs_remapped['BTCUSDT']=json_decode(json_encode(['ass'=>'ass']));
        $trading_pairs_remapped['BTCUSDT']->statistics=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',(time()-3600*24*2)),'date_end'=>date('Y-m-d H:i:s',(time())),'limit'=>999])->data;

        $Companies=Campaign::find()->where(['id'=>$id])->one();

        $statistics=[];
        $date=time();
        for($i=$date-7*24*3600;$i<=$date+10;$i+=12*3600){
            //$statistics['balances'][]=$Companies->getBalanceDate($i);
        }

        return $this->render("orders", [
            'date_start' => $date_start,
            'campaign' => $Companies,
            'trading_pairs'=>$trading_pairs,
            'orders'=>$orders,
            'trading_pairs_remapped'=>$trading_pairs_remapped,
            'statistics'=>$statistics,
            'trading_pairs'=>$trading_pairs_remapped
        ]);
    }

    public function actionIndex3(){
	    $balances=[];
	    $date_start=strtotime('2020-01-01 00:00:00');
	    $date_end=time();
	    for($i=$date_start; $i<$date_end;$i+=6*3600){
            $balances[]=DemoBalance::find()->where(['<','timestamp',date('Y-m-d  H:i:s',$i)])->orderBy('id desc')->limit(1)->one();
        }
        return $this->render("index3", [
            'balances'=>$balances,
        ]);
    }

    public function actionNew(){
	    if(Yii::$app->request->isPost){
	        if($this->update())
	            return $this->redirect('/trader2');
        }

	    return $this->render('edit');
    }

    public function actionEdit($id){
        if(Yii::$app->request->isPost){
            if($this->update())
                return $this->redirect('/trader2');
        }
	    $company=Campaign::findOne($id);
        return $this->render('edit',compact('company'));
    }

    public function update(){
        $post=Yii::$app->request->post();

        $company=Campaign::findOne($post['id']);
        unset($post['id']);
        if(empty($company)){
            $company=new Campaign();
            $company->created_at=date('Y-m-d H:i:s',time());
        }
        print_r($post);
        $company->attributes=$post;
        if($company->save())
            return 1;
        else{
            print_r($company->errors);
            return 0;
        }
    }
    public function actionPairs(){
        if(Yii::$app->request->isPost){
            $res=ApiRequest::statistics('v1/trader2/update',Yii::$app->request->post());
            print_r($res);
        }

        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['limit'=>999]);
        //print_r(ArrayHelper::toArray($trading_pairs));
        $trading_pairs=$trading_pairs->data;



        return $this->render("pairs", [
            'trading_pairs'=>$trading_pairs,

        ]);
    }

    public function actionPossibility(){
	    $get=Yii::$app->request->get();
	    if(isset($get['symbol']))
	        $symbol=$get['symbol'];
	    else
	        $symbol='ETHBTC';

        if(isset($get['percent_drop']))
            $percent_drop=$get['percent_drop'];
        else
            $percent_drop='0.006';

        if(isset($get['date_start']))
            $date_start=$get['date_start'];
        else
            $date_start='2019-01-01';

        $date_end=date('Y-m-d', strtotime($date_start . ' +1 month'));


        if(isset($get['percent_bounce']))
            $percent_bounce=$get['percent_bounce'];
        else
            $percent_bounce='0.001';

        if(isset($get['percent_profit']))
            $percent_profit=$get['percent_profit'];
        else
            $percent_profit='0.004';
        if(isset($get['timeout']))
            $timeout=$get['timeout'];
        else
            $timeout=4*3600;

	    $data=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>$symbol,'date_start'=>$date_start,'date_end'=>$date_end]);
	    $data=json_decode(json_encode($data->data),true);

	    return $this->render("possibility", [
            'data'=>$data,
            'pair'=>$symbol,
            'percent_drop'=>$percent_drop,
            'percent_bounce'=>$percent_bounce,
            'percent_profit'=>$percent_profit,
            'timeout'=>$timeout,
            'date_start'=>$date_start,
            'date_end'=>$date_end,
        ]);
    }
     public function actionCmc(){
	    $coin=ApiRequest::statistics('v1/coin-market-cap/coin',[]);
	    $markets=ApiRequest::statistics('v1/coin-market-cap/market',[]);
         return $this->render("cmc", [
             'coins'=>$coin->data,
             'markets'=>$markets->data,
         ]);
     }


     public function actionPdf(){

            require '../../vendor/fpdf/fpdf/src/Fpdf/Fpdf.php';
            $get =Yii::$app->request->get();

         $date_start=strtotime(date("Y-m-d H:i:s",strtotime($get['date_start'])));

         $date_end= strtotime(date('Y-m-d H:i:s',$date_start-7*24*3600));
         $limit_date_end=strtotime('2020-01-01 00:00:00');

         $balances=[];

         for($i=$date_end; ($i<$date_start );$i+=6*3600){
             if($i>$limit_date_end)
                 continue;

             $balances[]=DemoBalance::find()->where(['<','timestamp',date('Y-m-d H:i:s',$i)])->orderBy('id desc')->limit(1)->one();
         }



         $demo_orders=DemoTask::find()->where(['<','time',$date_start])
         ->andWhere(['>','time',$date_end])
         ->andWhere(['>','time',$limit_date_end])
         //->createCommand()->rawSql;
         ->all();
//          echo $demo_orders;
//         die();



         $total_usdt=0;
         foreach ($balances[0]->balances as $bb){
             $total_usdt+=$bb['value'];
         }


         $end_eq=count($balances)-1;

//        print_r($balances[$end_eq]);
//        die();
         $end_usdt=0;
         foreach ($balances[$end_eq]->balances as $bbb){
             $end_usdt+=$bbb['value'];
         }


         $pdf = new FPDF();
         //$pdf = new \FPDF();
         $pdf->AddPage();
         $pdf->SetFont('Arial','B',16);
         $pdf->Multicell(300,10,"Start Time: ".$balances[$end_eq]->timestamp." budget: $".number_format($end_usdt,2) );
         $pdf->Multicell(300,10,"Close Time: ".$balances[0]->timestamp." summary: $".number_format($total_usdt,2) );

         $pdf->Multicell(300,10,"Profit:  $".number_format($total_usdt-$end_usdt,2) );
         $pdf->SetFont('Arial','',8);
         for($i=0;$i<count($demo_orders);$i++){
             $demo_order=$demo_orders[$i];
             if($demo_order->sell==1) continue;
             if(isset($demo_orders[$i+1]) ){
                 $pdf->Multicell(300,5,"Order #$demo_order->id -> ".$demo_orders[$i+1]->id." Time: ".date('Y-m-d H:i:s',$demo_order->time)." rate: $demo_order->rate -->".$demo_orders[$i+1]->rate." $demo_order->currency_one $demo_order->currency_two profit: ".(($demo_orders[$i+1]->rate-$demo_order->rate)/$demo_order->rate*100)."%");

             }
         }
         //         $pdf->Multicell(40,10,'Hello World!');

//         $pdf->Multicell(40,10,'Hello World!');
         $pdf->Output('D','filename.pdf');

         //return \Yii::$app->response->sendFile('wtf.pdf');
     }
    public function actionFixBalance(){
	    $start_date='2019-01-01 00:00:00';

	    $end_date='2020-01-01';

	    $start_balance=[
	        "BTC"=>[
	            "value"=>1000000,
                "tokens"=>276.6
            ],
            "USDT"=>[
                "value"=>0,
                "tokens"=>0
            ]
        ];

	    for ($i=strtotime($start_date);$i<strtotime($end_date);$i+=6*3600){
            $orders=DemoTask::find()->where(['>','time',$i])->andWhere(['<','time',$i+6*3600])->orderBy([
                'time' => SORT_ASC,
                'id'=>SORT_ASC
            ])
                //->createCommand()->rawSql;
                ->all();
            foreach ($orders as $order){
                
            }
        }

    }
     public function actionImport(){
	    die();
	    return;
         ini_set('max_execution_time', 900);
         ini_set("memory_limit", "0");
	        $lines=[];



         if (($handle = fopen('ETH.csv', 'r')) !== FALSE) { // Check the resource is valid

             while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) { // Check opening the file is OK!

                 $lines[]=$data;
             }
             fclose($handle);
         }


         for($i=0;$i<count($lines);$i+=2){
            if($lines[$i][3]==1)
                continue;
            if(isset($lines[$i+1])){
                if($lines[$i][5]<$lines[$i+1][5]){
                    echo $lines[$i][5]." ",$lines[$i+1][5];
                    $this->inserLine($lines[$i]);
                    $this->inserLine($lines[$i+1]);
                    //die();

                }
            }
         }
     }
     private function inserLine($line){
        $demoTask=new DemoTask();

        $demoTask->company_id=$line[1];
        $demoTask->status=$line[2];
        $demoTask->sell=$line[3];
        $demoTask->tokens_count=$line[4];
        $demoTask->rate=$line[5];
        $demoTask->progress=$line[6];
        $demoTask->data_json=$line[7];
        $demoTask->external_id=1;
        $demoTask->time=$line[9];
        $demoTask->created_at=$line[10];
        $demoTask->loaded_at=$line[11];
        $demoTask->currency_one=$line[12];
        $demoTask->currency_two=$line[13];

        $demoTask->save();

        print_r($demoTask->errors);
     }



     public function actionIndex4(){
	    $get=Yii::$app->request->get();
	    if(isset($get['date_start'])){
            $date_start=date("Y-m-01 00:00:00",$get['date_start']);
        }
	    else
            $date_start=date("Y-m-01 00:00:00",time());


	    $now_limit=time()-24*3600;

         $date_end = date("Y-m-d H:i:s", strtotime($date_start." +1 month" ));

         $events=[];
         for($i=strtotime($date_start); $i<strtotime($date_end);$i+=24*3600){
             //echo  date('Y-m-d H:i:s', $i)." ".date('Y-m-d H:i:s', $now_limit);
             if($i>$now_limit)
                break;
             //echo  "time ";
             //continue;
             $orders=DemoTask::find()->where(['>','time',$i+2*3600])
                 ->andWhere(['<','time',$i+24*3600+2*3600])
                 //->andWhere(['<','time',$now_limit-2*3600])
                 ->orderBy([
                     'time' => SORT_ASC,
                     'id'=>SORT_ASC
                 ])
                 ->limit(400)
//                 ->createCommand()->rawSql;
//             echo $orders." ";

                 ->all();

             $trade_profit=0;
             for($j=0;$j<count($orders);$j++){
                 $t=$orders[$j];
                 if($t->sell==1) continue;
                 if($orders[$j+1]->sell==1 && $orders[$j+1]->currency_one==$t->currency_one){

                     $money_was=$orders[$j]->rate*$orders[$j]->tokens_count;
                     $money_now=$orders[$j+1]->rate*$orders[$j]->tokens_count;

                     //if($t->status==\common\models\DemoTask::STATUS_CREATED)
                     //print_r($trading_pairs_remapped[$t->currency_one.$t->currency_two]->statistics->data->now->bid);
                     //$money_now=$trading_pairs_remapped[$t->currency_one.$t->currency_two]->statistics->data->now->bid*$orders[$j+1]->tokens_count;
//                            if($t->status==\common\models\DemoTask::STATUS_COMPLETED)
//                                $money_now=$t->rate+$orders[$j+1]->tokens_count;

                     $perccent=($money_now-$money_was)/$money_now*100;
                     $trade_profit+=($perccent);
                     //$trade_profit+=$perccent;
                 }
             }
             $events[]=[
                 'title'=>"daily profit: ".number_format($trade_profit/10,2)."%",
                 'backgroundColor'=>"#90ee90",
                 'textColor'=>"#ffffff",
                 'start'=>date('Y-m-d 23:55:00',$i+4*3600)
             ];
             $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'limit'=>10]);
             $trading_pairs=$trading_pairs->data;
             $period=Trading::getPeriod();

             $orders=Task::find()->orderBy('id desc')->limit(100)->all();

             foreach ($trading_pairs as $trading_pair){
                 $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
                 $trading_pair->statistics=$tmp;
             }

             $trading_pairs_remapped=[];
             foreach ($trading_pairs as $trading_pair){
                 $trading_pairs_remapped[$trading_pair->trading_paid]=$trading_pair;
             }

             $data1=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',($i)),'date_end'=>date('Y-m-d H:i:s',($i)+900),'limit'=>1]);
             reset($data1->data); //Ensure that we're at the first element
             $key = key($data1->data);
             $open1=$data1->data->{$key}->open;




             $data2=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',($i)+24*3600),'date_end'=>date('Y-m-d H:i:s',($i)+24*3600+900),'limit'=>1]);
             reset($data2->data); //Ensure that we're at the first element
             $key = key($data2->data);
             $open2=$data2->data->{$key}->open;



             $btc_twitch=strval(number_format(($open2-$open1)/$open1*100,2))." % / $".($open2-$open1);

             $events[]=[
                 'title'=>"btc twitch: ".$btc_twitch,
                 'backgroundColor'=>($open2-$open1)>0?"#90ee90":"#f08080",
                 'textColor'=>"#000000",
                 'start'=>date('Y-m-d 23:55:00',$i+4*3600)
             ];
         }
         //die();


         return $this->render("index4", [
            'date_start'=>$date_start,
             'events'=>$events
         ]);


     }

     public function actionUsdtWithAll($id){
        $text_info='';
         ob_start();

	    //отменим все ордер
         $tasks=Task::find()->where(['campaign_id'=>$id])->andWhere(['not in','status',[1,4,5]])->all();
         foreach ($tasks as $task){
             $task->is_user=1;
             $task->cancelOrder();
         }
	    $campaign=Campaign::findOne($id);

         $campaign->getUsdtWithAll();
         $out1 = ob_get_contents();
         ob_end_clean();
         return $this->render("usdt_with_all",['info'=>$out1]);
     }

    public function actionUsdtWithEntrance($id){
        //отменим все ордер
        ob_start();
//        $tasks=Task::find()->where(['campaign_id'=>$id,'sell'=>0])->andWhere(['not in','status',[0,1,4,5]])->all();
//        foreach ($tasks as $task){
//            $task->is_user=1;
//            $task->cancelOrder();
//        }
        $campaign=Campaign::findOne($id);

        $campaign->getUsdtWithEntrance();
        $out1 = ob_get_contents();
        ob_end_clean();
        return $this->render("usdt_with_entrance");
    }

    public function actionEntranceWithUsdt($id){
        ob_start();
        $campaign=Campaign::findOne($id);

        $campaign->getEntranceWithUsdt();
        $out1 = ob_get_contents();
        ob_end_clean();
        return $this->render("entrance_usdt_with");
    }

    public function actionManualOrder(){
	    $post=Yii::$app->request->post();

	    $campaign=Campaign::findOne($post['campaign_id']);

	    $currency_one=Currency::find()->where(['symbol'=>$post['currency_one']])->limit(1)->one();
	    $currency_two=Currency::find()->where(['symbol'=>$post['currency_two']])->limit(1)->one();

        $trading_pair=ApiRequest::statistics('v1/trader2/info',['pair'=>$currency_one->symbol.$currency_two->symbol]);

        $campaign->sellTask($currency_one,$currency_two,$trading_pair->data,$post['is_buy'],$post['percent']/100,$post['profit']/100,1);
    }

    public function actionLoadPairStatistics(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    $post=Yii::$app->request->post();
	    $tasks=Task::find()->where(['currency_one'=>$post['currency_one'],'currency_two'=>$post['currency_two'],'campaign_id'=>$post['id']])
            ->andWhere(['>','time',time()-3600*24*7])->orderBy('time desc')->all();
	    return $tasks;
    }
}
