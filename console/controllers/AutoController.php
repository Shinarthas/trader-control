<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use common\models\Log;
use Yii;
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
		
		$tasks = Task::find()->with(['promotion', 'promotion.market', 'promotion.accounts'])->where(['<=','time',time()])->andWhere(['>=','time',time()-600])->andWhere(['status'=>Task::STATUS_NEW])->orderBy("time")->limit(8)->all();

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
	
	public function actionMakeTask($id) {
		$task = Task::findOne($id);  

		$r = $task->make();
		print_r($r);
	}
	
	public function actionCheckPrice($id) {
		$p = Promotion::findOne($id);
		$res=$p->checkPrice();
		print_r($res);
	}
	
	public function actionCreateHourTasks() {
        set_error_handler(array(new Log(), 'logError'));//process errors
		foreach(Promotion::find()->where(['enabled'=>1])->all() as $promotion)
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