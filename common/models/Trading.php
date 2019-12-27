<?php


namespace common\models;


use common\components\ApiRequest;
use yii\helpers\ArrayHelper;

class Trading
{
    public static function getPeriod(){
        $hours=date('H',time());
        $period=intval(intval($hours)/6);

        $time=intval(strtotime(date('Y-m-d',time())))+$period*3600*6;
        return $time;
    }
    public static function index($trading_pairs,$btc_usdt){


        self::closeBalances($trading_pairs,$btc_usdt);
        self::closeOutdated($trading_pairs,$btc_usdt);
        self::placeNew($trading_pairs,$btc_usdt);
        self::calculateBalance($trading_pairs,$btc_usdt);
    }

    public static function closeBalances($trading_pairs,$btc_usdt){
        //finish trading if it's 20:00 Hong-Kong
        $period=Trading::getPeriod();

            //if(true){
            $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->andWhere(['<>','period',$period])->all();
            $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
            $new_balance_json=$balance->balances;
            $new_balance_json['USDT']=$balance->balances['USDT'];
            $new_balance_json['BTC']=$balance->balances['BTC'];
            foreach ($tasks as $task){
                foreach ($trading_pairs as $trading_pair){
                    if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){

                        //$new_balance_json['USDT']['tokens']+=$trading_pair->bid*$task->tokens_count;
                        $new_balance_json['BTC']['tokens']+=$trading_pair->bid*$task->tokens_count/$btc_usdt->{'now'}->bid;
                        //$new_balance_json['USDT']['value']+=$trading_pair->bid*$task->tokens_count;
                        $new_balance_json['BTC']['value']+=$trading_pair->bid*$task->tokens_count;
                        //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->bid;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->bid;

                        $task->rate=$trading_pair->bid;
                        $task->status=DemoTask::STATUS_CANCELED;
                        $task->save();
                    }
                }
            }
            if (count($tasks) || (time()-10<$period && time()+15>$period)){//типо новый период и нам нужно закрыться
                //self::getUsdtWithBtc();
            }

            $new_balance=new DemoBalance();
            $new_balance->period=Trading::getPeriod();
            $new_balance->balances=$new_balance_json;
            $new_balance->timestamp=date("Y-m-d H:i:s");

            $new_balance->save();
            //die();
            /*
            $balamce_day_ago=DemoBalance::find()->where(['<','timestamp',date('Y-m-d H:i:s',time()-5*60)])
                ->orderBy('id desc')->limit(1)->one();
            if (empty($balamce_day_ago))
                die();
            //сколько у нас сейчас
            $tmp_usdt1=0;
            $tmp_balance1=$new_balance->balances;
            foreach ($tmp_balance1 as $symbol=>$value){
                $tmp_usdt1+=$value['value'];
            }
            //сколько было до начала торгов
            $tmp_usdt2=0;
            $tmp_balance2=$balamce_day_ago->balances;
            foreach ($tmp_balance2 as $symbol=>$value){
                $tmp_usdt2+=$value['value'];
            }

            if($tmp_usdt2<$tmp_usdt1){
                //если у нас профит скинуть в банк
                if($tmp_usdt1>1000000){
                    $withdraw=$tmp_usdt1-1000000;
                    $new_balance_json['USDT']=['tokens'=>'1000000','value'=>1000000];
                    $new_balance->balances=$new_balance_json;
                    $new_balance->timestamp=date("Y-m-d H:i:s");
                    $new_balance->save();
                    DemoProfit::create($withdraw);
                }else{
                    Log::log([
                        'value'=>$tmp_usdt2-$tmp_usdt1
                    ],'withdraw','profit no withdraw');
                }
            }else{
                $new_balance_json['USDT']=['tokens'=>'1000000','value'=>1000000];
                $new_balance->balances=$new_balance_json;
                $new_balance->timestamp=date("Y-m-d H:i:s");
                $new_balance->save();

                $losses=$tmp_usdt1-$tmp_usdt2;
                DemoProfit::create($losses);
            }
            */
            //die();

        //end finish trading if it's 20:00 Hong-Kong
    }

    public static function closeOutdated($trading_pairs,$btc_usdt){
        //cancel outdated
        $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
        $new_balance_json=$balance->balances;
        $new_balance_json['USDT']=$balance->balances['USDT'];
        $new_balance_json['BTC']=$balance->balances['BTC'];
        foreach ($tasks as $task){
            if(time()-$task->created_at>4*3600){
                foreach ($trading_pairs as $trading_pair){
                    if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){
                        echo " CANCELED ORDER ";
                        //$new_balance_json['USDT']['tokens']+=$trading_pair->statistics->now->bid*$task->tokens_count;
                        $new_balance_json['BTC']['tokens']+=$trading_pair->statistics->now->bid*$task->tokens_count/$btc_usdt->{'now'}->bid;
                        //$new_balance_json['USDT']['value']+=$trading_pair->statistics->now->bid*$task->tokens_count;
                        $new_balance_json['BTC']['value']+=$trading_pair->statistics->now->bid*$task->tokens_count;

                        //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->statistics->{'now'}->bid;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->statistics->{'now'}->bid*$btc_usdt->{'now'}->bid;

                        $task->rate=$trading_pair->statistics->now->bid;
                        $task->status=DemoTask::STATUS_CANCELED;
                        $task->save();
                    }
                }
            }
        }
        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
        // end cancel outdated
    }

    public static function placeNew($trading_pairs,$btc_usdt){
        //place new
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=[];
        $new_balance_json=$balance->balances;
        $btc_value=$balance->balances['BTC']['value'];
        $summary_usdt=0;
        foreach ($balance->balances as $currency=>$value){
            $summary_usdt+=$value['value'];
        }

        foreach ($trading_pairs as $trading_pair){
            $bid10=$trading_pair->statistics->{'10min'}->bid;
            $bid5=$trading_pair->statistics->{'5min'}->bid;
            $bid_now=$trading_pair->statistics->{'now'}->bid;

            $random_per=mt_rand() / mt_getrandmax()/10;

            if($trading_pair->statistics->{'now'}->bid!=0)
                //echo $btc_value." ";
                if(self::findThresholdBreak(0.06,0.01,$trading_pair->statistics) && $btc_value>=$summary_usdt*$random_per){
                    //if(true && $btc_value>=abs($summary_usdt*$random_per)){
                    $task_buy=new DemoTask();

                    $task_buy->company_id=1;
                    $task_buy->status=5;//  потому что мы как бы продали
                    $task_buy->sell=0;


                    //закупаемся на 10%
                    $task_buy->tokens_count=$summary_usdt*$random_per/$trading_pair->statistics->{'now'}->bid;
                    if($task_buy->tokens_count<0.1)
                        continue;
                    //отнимаем от нашего баланса
                    /* // old usdt
                    $new_balance_json['USDT']['tokens']=$new_balance_json['USDT']['tokens']-$summary_usdt*$random_per;
                    $new_balance_json['USDT']['value']=$new_balance_json['USDT']['value']-$summary_usdt*$random_per;
                    if(isset($new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)])){
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']=
                            $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']+$summary_usdt*$random_per/$trading_pair->statistics->{'now'}->bid;
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']=
                            $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']+$summary_usdt*$random_per;
                    }else{
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']=$summary_usdt*$random_per/$trading_pair->statistics->{'now'}->bid;
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']=$summary_usdt*$random_per;
                    }*/
                    $new_balance_json['BTC']['tokens']=$new_balance_json['BTC']['tokens']-$summary_usdt*$random_per/$btc_usdt->{'now'}->bid;
                    $new_balance_json['BTC']['value']=$new_balance_json['BTC']['value']-$summary_usdt*$random_per;
                    if(isset($new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)])){
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']=
                            $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']+$summary_usdt*$random_per*$trading_pair->statistics->{'now'}->bid/$btc_usdt->{'now'}->bid;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']=
                            $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']+$summary_usdt*$random_per;
                    }else{
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']=$summary_usdt*$random_per*$trading_pair->statistics->{'now'}->bid/$btc_usdt->{'now'}->bid;
                        $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']=$summary_usdt*$random_per;
                    }

                    $btc_value-=$summary_usdt*$random_per;

                    $task_buy->rate=$trading_pair->statistics->{'now'}->bid;
                    $task_buy->period=Trading::getPeriod();
                    $task_buy->progress=100;
                    $task_buy->data_json=[];
                    $task_buy->time=time();
                    $task_buy->created_at=time();
                    $task_buy->loaded_at=time();
                    $task_buy->currency_one=str_replace('BTC','',$trading_pair->trading_paid);
                    $task_buy->currency_two='BTC';
                    $task_buy->external_id='1';
                    $task_buy->data_json="{'asd':'asd'}";
                    $task_buy->save();
                    Log::log(ArrayHelper::toArray($task_buy),'info','buy order place');




                    //и сразу выставляем на продажу
                    $task_sell=new DemoTask();

                    $task_sell->company_id=1;
                    $task_sell->status=2;//  потому что мы как бы продали
                    $task_sell->sell=1;

                    //закупаемся на 10%
                    $task_sell->tokens_count=$summary_usdt*$random_per/$trading_pair->statistics->{'now'}->bid;
                    if($task_sell->tokens_count<$random_per)
                        continue;

                    $task_sell->rate=$trading_pair->statistics->{'now'}->ask*1.04;
                    $task_sell->period=Trading::getPeriod();
                    $task_sell->progress=0;
                    $task_sell->data_json=[];
                    $task_sell->time=time();
                    $task_sell->created_at=time();
                    $task_sell->loaded_at=time();
                    $task_sell->currency_one=str_replace('BTC','',$trading_pair->trading_paid);
                    $task_sell->currency_two='BTC';
                    $task_sell->external_id='1';
                    $task_sell->data_json="{'asd':'asd'}";
                    $task_sell->save();
                    Log::log(ArrayHelper::toArray($task_sell),'info','sell order place');


                }
        }
        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
        // end place new
    }

    public static function calculateBalance($trading_pairs,$btc_usdt){

        //calculate balance
        //get open orders
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=[];
        $new_balance_json['USDT']=$balance->balances['USDT'];
        $new_balance_json['BTC']=$balance->balances['BTC'];
        $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
        foreach ($tasks as $task){
            foreach ($trading_pairs as $trading_pair){
                if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){
                    if(isset($new_balance_json[$task->currency_one])){
                        $new_balance_json[$task->currency_one]['tokens']+=$task->tokens_count;
                        $new_balance_json[$task->currency_one]['value']+=$task->tokens_count*$trading_pair->statistics->now->bid;
                    }else{
                        $new_balance_json[$task->currency_one]['tokens']=$task->tokens_count;
                        $new_balance_json[$task->currency_one]['value']=$task->tokens_count*$trading_pair->statistics->now->bid;
                    }
                }
            }
        }
        $new_balance_json['BTC']['value']=$new_balance_json['BTC']['tokens']*$btc_usdt->{'now'}->bid;
        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s",time());
        $new_balance->save();


    }

    public static function closeTasks($trading_pairs,$btc_usdt){
        $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
        $new_balance_json=$balance->balances;
        $new_balance_json['USDT']=$balance->balances['USDT'];
        $new_balance_json['BTC']=$balance->balances['BTC'];
        foreach ($tasks as $task){
            foreach ($trading_pairs as $trading_pair){
                if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){

                    //$new_balance_json['USDT']['tokens']+=$trading_pair->bid*$task->tokens_count;
                    $new_balance_json['BTC']['tokens']+=$trading_pair->bid*$task->tokens_count/$btc_usdt->{'now'}->bid;
                    //$new_balance_json['USDT']['value']+=$trading_pair->bid*$task->tokens_count;
                    $new_balance_json['BTC']['value']+=$trading_pair->bid*$task->tokens_count;

                    //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                    $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count/$btc_usdt->{'now'}->bid;
                    //$new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->bid;
                    $new_balance_json[str_replace('BTC','',$trading_pair->trading_paid)]['value']-=$task->tokens_count/$btc_usdt->{'now'}->bid*$trading_pair->bid;

                    $task->rate=$trading_pair->bid;
                    $task->status=DemoTask::STATUS_CANCELED;
                    $task->save();
                }
            }
        }
        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
    }
    public static function getBtcWithUsdt($save=0){
        $btc_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>'BTCUSDT']);
        $btc_usdt=$btc_usdt->data;

        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=$balance->balances;

        $task_buy=new DemoTask();

        $task_buy->company_id=1;
        $task_buy->status=5;//  потому что мы как бы продали
        $task_buy->sell=0;


        //вот тут
        $task_buy->tokens_count=($new_balance_json['USDT']['value']-$save)/$btc_usdt->{'now'}->bid;
        $task_buy->rate=$btc_usdt->{'now'}->bid;
        //и закинули на счет
        $new_balance_json['BTC']['tokens']=$new_balance_json['BTC']['tokens']+$task_buy->tokens_count;
        $new_balance_json['BTC']['value']=$new_balance_json['BTC']['value']+$task_buy->tokens_count*$task_buy->rate;
        //мы скупились на все
        $new_balance_json['USDT']['tokens']=$save;
        $new_balance_json['USDT']['value']=$save;

        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();

    }

    public static function getUsdtWithBtc($save=1000000){
        $btc_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>'BTCUSDT']);
        $btc_usdt=$btc_usdt->data;

        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=$balance->balances;

        $task_sell=new DemoTask();

        $task_sell->company_id=1;
        $task_sell->status=5;//  потому что мы как бы купили
        $task_sell->sell=1;


        if($new_balance_json['BTC']['value']<=$save)//мы не можем их вывести
            return;
        //вот тут
        $task_sell->tokens_count=($new_balance_json['BTC']['value']-$save)/$btc_usdt->{'now'}->bid;
        $task_sell->rate=$btc_usdt->{'now'}->bid;
        $task_sell->period=Trading::getPeriod();
        $task_sell->progress=100;
        $task_sell->data_json=[];
        $task_sell->time=time();
        $task_sell->created_at=time();
        $task_sell->loaded_at=time();
        $task_sell->currency_one="BTC";
        $task_sell->currency_two='USDT';
        $task_sell->external_id='1';
        $task_sell->data_json="{'asd':'asd'}";
        $task_sell->save();
        //и закинули на счет
        $new_balance_json['USDT']['tokens']=$new_balance_json['USDT']['tokens']+$task_sell->tokens_count*$task_sell->rate;
        $new_balance_json['USDT']['value']=$new_balance_json['USDT']['value']+$task_sell->tokens_count*$task_sell->rate;
        //мы скупились на все
        $new_balance_json['BTC']['tokens']=$save/$btc_usdt->{'now'}->bid;
        $new_balance_json['BTC']['value']=$save;

        $new_balance=new DemoBalance();
        $new_balance->period=Trading::getPeriod();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
        DemoProfit::create($new_balance_json['BTC']['value']-$save);
    }

    public static function reset(){
        \Yii::$app->db->createCommand()->truncateTable('demo_balance')->execute();
        \Yii::$app->db->createCommand()->truncateTable('demo_task')->execute();

        $db=new DemoBalance();
        $db->period=Trading::getPeriod();
        $db->balances=['USDT'=>['value'=>0,'tokens'=>0],'BTC'=>['value'=>1000615,'tokens'=>139.86]];
        $db->save();
    }

    public static function findThresholdBreak($percent_drop,$percent_bounce,$statistics){
        $bid240=$statistics->{'240min'}->bid;
        $bid120=$statistics->{'120min'}->bid;
        $bid60=$statistics->{'60min'}->bid;
        $bid40=$statistics->{'40min'}->bid;
        $bid30=$statistics->{'30min'}->bid;
        $bid20=$statistics->{'20min'}->bid;
        $bid15=$statistics->{'15min'}->bid;
        $bid10=$statistics->{'10min'}->bid;
        $bid5=$statistics->{'5min'}->bid;
        $bid_now=$statistics->{'now'}->bid;

        $sequence=[$bid5,$bid10,$bid15,$bid20,$bid30,$bid40,$bid60,$bid120,$bid240];
        //echo "---------------"."\n";
        $count_sequence=count($sequence);
        for ($i=0;$i<$count_sequence;$i++){
            $time1=$sequence[$i];//всегда должна быть меньше чем время t2
            for ($j=$i+1;$j<$count_sequence;$j++){
                $time2=$sequence[$j];
                //echo ($time2-$time1)/$time1." ".($bid_now-$time1)/$time1."\n";
                if(($time2-$time1)/$time1>$percent_drop && ($bid_now-$time1)/$time1>$percent_bounce )
                    return true;
            }
        }
        return false;

    }
}