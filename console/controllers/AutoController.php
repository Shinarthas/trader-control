<?php
namespace console\controllers;

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

		if(date("i",time())%10==0)
			$promotions_active = Promotion::find()->all();
		else
			foreach($tasks as $t) 
				$promotions_active[$t->promotion_id] = $t->promotion;
		

		$last_currency_two = 0;
		foreach($promotions_active as $p) {
			if($last_currency_two == $p->currency_two)
				continue;
			
			$p->checkPrice();
			$last_currency_two = $p->currency_two;
		}
		
		foreach($tasks as $t) {
			if(time() - $start <= 110)
				$t->make();
		}
	}
	
	public function actionMakeTask() {
		$task = Task::findOne(1134); 
		$r = $task->make();
		print_r($r);
	}
	
	public function actionCreateHourTasks() {
		foreach(Promotion::find()->where(['enabled'=>1])->all() as $promotion)
		{
			if($promotion->settings['day_tasks']==0)
				$promotion->createHourTasks(time());
			else
			{
				if(date("H", time()) == 0)
					$promotion->createDayTasks(time());
			}
		}
	}
	
	public function actionCalcProgress() {

		if(date("i", time()) == 20 OR date("i", time()) == 40)
			$promotions = Promotion::find()->where(['enabled'=>1, 'mode'=>Promotion::MODE_FAST_EARN])->all();
		else
			$promotions = Promotion::find()->where(['enabled'=>1])->all();
		
		foreach($promotions as $promotion) {
			if($promotion->settings['disable_balance_check']==1)
				continue;
				
			$promotion->clearOldOrders();
		}
	}
	
	public function actionCancelOrder() {
		$task = Task::findOne(167);
		$task->cancelOrder();
	}
}