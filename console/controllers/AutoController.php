<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use common\models\DemoBalance;
use common\components\ApiRequest;
use common\components\Executor;
use common\models\Company;
use common\models\DemoProfit;
use common\models\DemoTask;
use common\models\Log;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Task;
use common\models\Promotion;

class AutoController extends Controller
{
	public function actionIndex()
	{	
		$start = time();
		
		$promotions_active = [];
		
		$tasks = Task::find()->with(['promotion', 'promotion.market', 'promotion.accounts'])->where(['<=','time',time()])->andWhere(['>=','time',time()-600])
            ->andWhere(['status'=>Task::STATUS_NEW])->orderBy("time")->limit(8)->all();

		if(date("i",time())%10==0)//что? зачем?
			$promotions_active = Promotion::find()->all();
		else
			foreach($tasks as $t) 
				$promotions_active[$t->promotion_id] = $t->promotion;

        $promotions_active = Promotion::find()->all();

		$last_currency_two = 0;
        $last_market_id = 0;
		foreach($promotions_active as $p) {
			if($last_currency_two == $p->currency_two && $last_market_id==$p->market_id)
				continue;
			$p->checkPrice();//это чтоб не делать чек курсса одной валюты много раз?
			$last_currency_two = $p->currency_two;
            $last_market_id = $p->market_id;
		}
		
		foreach($tasks as $t) {
			if(time() - $start <= 110)
				$t->make();
		}
	}
	public function actionPossibilityTask(){
	    //getting chances for promotions
        $possibilityTable=ApiRequest::statistics('v1/possibility/current',[]);
        //print_r($possibilityTable->data[0]);
        foreach ($possibilityTable->data as $possibility){
            echo $possibility->promotion_id." ";
            $promotion=Promotion::findOne($possibility->promotion_id);
            if($promotion->enabled && $promotion->mode==Promotion::MODE_POSSIBILITY &&
                $possibility->chance>=$promotion->settings['minimal_percent'])
                    Task::possibility($promotion);


        }
    }
    public function actionTrader2(){
        $trading_pairs=ApiRequest::statistics('v1/trader2/list',[]);
        $trading_pairs=$trading_pairs->data;

        //finish trading if it's 20:00 Hong-Kong
        if(date("H:i",time())=="20:00"){
        //if(true){
            $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
            $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
            $new_balance_json=$balance->balances;
            $new_balance_json['USDT']=$balance->balances['USDT'];
            foreach ($tasks as $task){
                    foreach ($trading_pairs as $trading_pair){
                        if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){

                            $new_balance_json['USDT']['tokens']+=$trading_pair->bid*$task->tokens_count;
                            $new_balance_json['USDT']['value']+=$trading_pair->bid*$task->tokens_count;

                            $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                            $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->bid;

                            $task->rate=$trading_pair->bid;
                            $task->status=DemoTask::STATUS_CANCELED;
                            $task->save();
                        }
                }
            }
            $new_balance=new DemoBalance();
            $new_balance->balances=$new_balance_json;
            $new_balance->timestamp=date("Y-m-d H:i:s");
            $new_balance->save();

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
            die();
        }
        //end finish trading if it's 20:00 Hong-Kong

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp->data;

        }

        //close balances if they triggered
        $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
        $new_balance_json=$balance->balances;
        $new_balance_json['USDT']=$balance->balances['USDT'];
        foreach ($tasks as $task){
            foreach ($trading_pairs as $trading_pair){
                if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){
                    if($task->rate<=$trading_pair->statistics->now->bid){
                        $new_balance_json['USDT']['tokens']+=$task->rate*$task->tokens_count;
                        $new_balance_json['USDT']['value']+=$task->rate*$task->tokens_count;

                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->statistics->{'now'}->bid;

                        $task->status=DemoTask::STATUS_COMPLETED;
                        $task->save();
                    }
                }
            }
        }
        $new_balance=new DemoBalance();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();

        //cancel outdated
        $tasks=DemoTask::find()->where(['sell'=>1,'status'=>DemoTask::STATUS_CREATED])->all();
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();
        $new_balance_json=$balance->balances;
        $new_balance_json['USDT']=$balance->balances['USDT'];
        foreach ($tasks as $task){
            if(time()-$task->created_at>4*3600){
                foreach ($trading_pairs as $trading_pair){
                    if($task->currency_one.$task->currency_two==$trading_pair->trading_paid){
                        echo " CANCELED ORDER ";
                        $new_balance_json['USDT']['tokens']+=$trading_pair->statistics->now->bid*$task->tokens_count;
                        $new_balance_json['USDT']['value']+=$trading_pair->statistics->now->bid*$task->tokens_count;

                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['tokens']-=$task->tokens_count;
                        $new_balance_json[str_replace('USDT','',$trading_pair->trading_paid)]['value']-=$task->tokens_count*$trading_pair->statistics->{'now'}->bid;

                        $task->rate=$trading_pair->statistics->now->bid;
                        $task->status=DemoTask::STATUS_CANCELED;
                        $task->save();
                    }
                }
            }
        }
        $new_balance=new DemoBalance();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
        // end cancel outdated



        //place new
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=[];
        $new_balance_json=$balance->balances;
        $usdt_value=$balance->balances['USDT']['value'];
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
                if(($bid10-$bid5)/$bid5>0.006 && ($bid_now-$bid5)/$bid5>0.001 && $usdt_value>=$summary_usdt*$random_per){
                //if(true && $usdt_value>=abs($summary_usdt*$random_per)){
                    $task_buy=new DemoTask();

                    $task_buy->company_id=1;
                    $task_buy->status=5;//  потому что мы как бы продали
                    $task_buy->sell=0;

                    //закупаемся на 10%
                    $task_buy->tokens_count=$summary_usdt*$random_per/$trading_pair->statistics->{'now'}->bid;
                    if($task_buy->tokens_count<0.1)
                        continue;
                    //отнимаем от нашего баланса
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
                    }
                    $usdt_value-=$summary_usdt*$random_per;

                    $task_buy->rate=$trading_pair->statistics->{'now'}->bid;
                    $task_buy->progress=100;
                    $task_buy->data_json=[];
                    $task_buy->time=time();
                    $task_buy->created_at=time();
                    $task_buy->loaded_at=time();
                    $task_buy->currency_one=str_replace('USDT','',$trading_pair->trading_paid);
                    $task_buy->currency_two='USDT';
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
                    $task_sell->progress=0;
                    $task_sell->data_json=[];
                    $task_sell->time=time();
                    $task_sell->created_at=time();
                    $task_sell->loaded_at=time();
                    $task_sell->currency_one=str_replace('USDT','',$trading_pair->trading_paid);
                    $task_sell->currency_two='USDT';
                    $task_sell->external_id='1';
                    $task_sell->data_json="{'asd':'asd'}";
                    $task_sell->save();
                    Log::log(ArrayHelper::toArray($task_sell),'info','sell order place');


                }
        }
        $new_balance=new DemoBalance();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s");
        $new_balance->save();
        // end place new



        //calculate balance
        //get open orders
        $balance=DemoBalance::find()->orderBy('id desc')->limit(1)->one();

        $new_balance_json=[];
        $new_balance_json['USDT']=$balance->balances['USDT'];
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
        $new_balance=new DemoBalance();
        $new_balance->balances=$new_balance_json;
        $new_balance->timestamp=date("Y-m-d H:i:s",time());
        $new_balance->save();

    }
	public function actionCompany(){
	    $companies=Company::find()->all();
	    $tmp=ApiRequest::statistics('v1/trader2/statistics',[]);
        print_r($tmp->status);
	    if(!$tmp->status)
            return;

        $statistics=$tmp->data;
        foreach ($companies as $company){
            foreach ($statistics->items as $stat){
                $company->check($stat);
            }
        }
    }
	public function actionMakeTask($id) {
		$task = Task::findOne($id);  

		$r = $task->make();
		print_r($r);
	}
	
	public function actionCheckPrice($id) {
		$p = Promotion::findOne($id);
		$res=$p->checkPrice();
		//возможно запрашивать по каждой валюте

	}
	
	public function actionCreateHourTasks() {
        set_error_handler(array(new Log(), 'logError'));//process errors
		foreach(Promotion::find()->where(['enabled'=>1])->andWhere(['not in', 'mode',[Promotion::MODE_POSSIBILITY]])->all() as $promotion)
		{
			if(!isset($promotion->settings['day_tasks']) ||  $promotion->settings['day_tasks']==0)
				$promotion->createHourTasks(time());
			else
			{
				if(date("H", time()) == 0)//про формат
					$promotion->createDayTasks(time());
			}
		}
	}
	
	public function actionCalcProgress() {

		if(date("i", time()) == 20 OR date("i", time()) == 40)// это ведь никогда не выполняется
			$promotions = Promotion::find()->where(['enabled'=>1, 'mode'=>Promotion::MODE_FAST_EARN])->all();
		else
			$promotions = Promotion::find()->where(['enabled'=>1])->all();
		
		foreach($promotions as $promotion) {
			if($promotion->settings['disable_balance_check']==1)
				continue;
				
			$promotion->clearOldOrders();
		}
	}
	
	public function actionCancelOrder($id) {
		$task = Task::findOne($id);
		$task->cancelOrder();
	}

	public function actionCreateAdmin() {
	    $admin = new \common\models\User;
	    $admin->username = 'admin';
	    $admin->email = 'admin';
	    $admin->setPassword("xklcbup32500");
	    $admin->generateAuthKey();
	    if($admin->save())
	        echo "OK";
	    else
	        print_r($admin->errors);

    }
    public function actionUpdate() {
        $order = Task::findOne(83);
        $order->attributes = [
            'id'=>'83',
            'canceled'=>'0',
            'external_id'=>'22724951',
            'progress'=>'0',
            'status'=>'5',

        ];
        $order->loaded_at = time();
        if(!$order->save())
            print_r($order->errors);
    }
}