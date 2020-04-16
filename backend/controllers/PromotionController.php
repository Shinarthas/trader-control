<?php
namespace backend\controllers;

use common\components\ApiRequest;
use common\models\Currency;
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

class PromotionController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function actionView($id) {
		
		$promotion = Promotion::findOne($id);
		if(isset($_POST['save'])) {
			$promotion->load($_POST);
			$promotion->settings = $_POST['settings'];
            $promotion->is_paid_proxy=$_POST['is_paid_proxy'];
			if($promotion->mode == $promotion::MODE_STABILIZE)
				$promotion->settings['speed'] = 0;
            $promotion->save();

		}
		
		if(isset($_POST['create_initial_tasks'])) 
			$promotion->createInitialTasks($_POST['count']);
			
		if(isset($_POST['stop'])) 
			$promotion->stop();
		
		if(isset($_POST['start'])) 
			$promotion->start();

		$statistics=ApiRequest::statistics('v1/promotion/get-statistics',['id'=>$promotion->id]);
		
		return $this->render("view", ['promotion' => $promotion,'statistics'=>$statistics]);
	}
    public function actionGraph2($id){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $data=Yii::$app->request->get();
        $data['promotion_id']=$id;
        $res=ApiRequest::statistics('v1/promotion/graph',$data);
    }
	public function actionGraph($id) {
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$promotion = Promotion::findOne($id);
		
		$name = $promotion->second_currency->symbol.'.'.$promotion->main_currency->symbol;
		
		$data = [];
		foreach(Task::find()->where(['promotion_id'=>$id, 'status'=>2])->andWhere(['sell'=>0])->andWhere(['!=','progress',0])->all() as $t)
		{
			$time = floor($t->time/86400)*86400;
			$data[$time]['value'] += $t->tokens_count * $t->rate;
			$data[$time]['rate'][] = $t->rate;
		}
		
		foreach(Task::find()->where(['promotion_id'=>$id, 'status'=>4])->andWhere(['sell'=>0])->andWhere(['!=','progress',0])->all() as $t)
		{
			$time = floor($t->time/86400)*86400;
			$data[$time]['value'] += $t->tokens_count * $t->rate * ($t->progress/100);
			$data[$time]['rate'][] = $t->rate;
		}
		
		
		foreach($data as $time => $t) {
			$xSeries[] = $time;
			$vl[] = (int)$t['value'];
			
			$hloc_temp_arr = [];
			$counter = -1;
			
			foreach($t['rate'] as $rate) {
			$counter++;
			
				if($rate > $hloc_temp_arr[0] OR $hloc_temp_arr[0] == 0 )
					 $hloc_temp_arr[0] = round($rate,2);
					 
				if($rate < $hloc_temp_arr[1] OR $hloc_temp_arr[1] == 0 )
					 $hloc_temp_arr[1] = round($rate,2);
			}
			//$hloc_temp_arr[2] = $hloc_temp_arr[0] - (($hloc_temp_arr[0]-$hloc_temp_arr[1])*0.2);
			//$hloc_temp_arr[3] = $hloc_temp_arr[1] + (($hloc_temp_arr[0]-$hloc_temp_arr[1])*0.2);
			$hloc_temp_arr[2] = round($t['rate'][0],2);
			$hloc_temp_arr[3] = round($t['rate'][$counter],2);
				
			$hloc[] = $hloc_temp_arr;
		}
		
		$out = [
			"hloc" => [$name => $hloc],
			"vl" => [$name => $vl], 
			"xSeries" => [$name => $xSeries],
			"info" => [
				"AAPL.US" => [
					"id" => $name,
					"short_name" => "Apple Inc.",
					"default_ticker" => "AAPL",
					"nt_ticker" => "AAPL.US",
					"currency" => "USD",
					"min_step" => "0.01000000",
					"lot" => "1.00000000",
			]]
		];
		
		return $out;
	}
	
	public function actionReport() {
		$promotion = Promotion::findOne($_GET['id']);
		
		$period_stat = [];
		
		$start = (((int)(time()/3600))*3600);
		
		$accounts = $promotion->accounts;
		
		for($i=0;$i<24;$i++) {
			$start-= 3600;
			
			$orders = [];
			$orders['planned'] = Task::find()->select("SUM(value) AS value")->where(['promotion_id'=>$promotion->id])->andWhere(['sell'=>0])->andWhere(['BETWEEN', 'time', $start, $start+3600])->scalar();
			$orders['created'] = Task::find()->select("SUM(value) AS value")->where(['promotion_id'=>$promotion->id])->andWhere(['sell'=>0])->andWhere(['!=','status', 1])->andWhere(['!=','status', 3])->andWhere(['BETWEEN', 'time', $start, $start+3600])->scalar();
			
			foreach(Task::find()->where(['promotion_id'=>$promotion->id, 'status'=>2])->andWhere(['!=','progress',0])->andWhere(['sell'=>0])->andWhere(['BETWEEN', 'time', $start, $start+3600])->all() as $t)
				$orders['succesfull']+= $t->value;
		
			foreach(Task::find()->where(['promotion_id'=>$promotion->id, 'status'=>4])->andWhere(['!=','progress',0])->andWhere(['sell'=>0])->andWhere(['BETWEEN', 'time', $start, $start+3600])->all() as $t)
				$orders['succesfull']+= $t->value * ($t->progress/100);

			foreach($accounts as $account) {
				foreach(AccountBalance::find()->where(['account_id'=>$account->id])->andWhere(['BETWEEN', 'loaded_at', $start+3600, $start+3900])->all() as $b) {
					if($b->currency_two ==0 OR $b->currency_two == $promotion->currency_two)
					$orders['balances'][$b->currency_id][$b->type]+= $b->value;
				}
			}
			
			$currency = CurrencyPrice::find()->where(['currency_one' => $promotion->currency_one , 'currency_two' => $promotion->currency_two])->andWhere(['BETWEEN', 'created_at', $start+3600, $start+3900])->one();
			$orders['price'] = ($currency->buy_price+$currency->sell_price)/2;
			
			$period_stat[] = [
				'start' => $start,
				'orders' => $orders,
				'balances' => []
			];
		}
		
		return $this->render("report", ['period_stat'=>$period_stat]);
	}
	
	public function actionAccounts() {
		$promotion = Promotion::findOne($_GET['id']);
		
		$p_a = [];
		foreach($promotion->promotionAccounts as $a)
			$p_a[] = $a->account_id;
		
		
		if(isset($_POST['save'])) {
			foreach($_POST['account'] as $k=>$v) {
				if($v==1) {
					if(in_array($k, $p_a))
						continue;
					$p_a_new = new PromotionAccount;
					$p_a_new->promotion_id = $promotion->id;
					$p_a_new->account_id = $k;
					$p_a_new->created_at = time();
					$p_a_new->save();
				}
				else {
					if(!in_array($k, $p_a))
						continue;
					$p_a_for_delete = PromotionAccount::find()->where(['promotion_id'=>$promotion->id, 'account_id'=>$k])->one();
					$p_a_for_delete->delete();
				}
			}
			
			$p_a = [];
			$promotion = Promotion::findOne($_GET['id']);
			foreach($promotion->promotionAccounts as $a)
				$p_a[] = $a->account_id;
		}
		
		
		$accounts = Account::find()->all();
		
		return $this->render("accounts", ['promotion'=>$promotion, 'p_a'=>$p_a, 'accounts'=>$accounts]);
	}
	
	public function actionAdd() {
		$promotion = new Promotion;
		$promotion->market_id = $_GET['market'];
		
		if(isset($_POST['save'])) {
			$promotion->load($_POST);
			$promotion->settings = $_POST['settings'];
			$promotion->created_at = time();
			$promotion->currency_one = 1;
			$promotion->is_paid_proxy = $_POST['is_paid_proxy'];

			if($promotion->mode == $promotion::MODE_STABILIZE)
				$promotion->settings['speed'] = 0;
			
			if($promotion->save()){}
				return $this->redirect("/market/". $_GET['market']);		
				
				

		}
		return $this->render("view", ['promotion' => $promotion]);
	}

	public function actionCancel(){
	    $get=Yii::$app->request->get();

	    $id=$get['id'];

	    $task=Task::findOne($id);
        $task->is_user=1;
        $task->cancelOrder();

        if($task->sell==1){
            $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'includes'=>'BTC']);
            $trading_pairs=$trading_pairs->data;

            foreach ($trading_pairs as $trading_pair){
                $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
                $trading_pair->statistics=$tmp->data;
            }

            $order_trading_pair=$task->currency_one.$task->currency_two;
            foreach ($trading_pairs as $trading_pair) {
                if ($trading_pair->trading_paid == $order_trading_pair) {//найдем нашу пару
                    $sell_task=new Task();
                    $sell_task->account_id = $task->account_id;
                    $sell_task->promotion_id=0;
                    $sell_task->status=0;
                    $sell_task->sell=1;
                    $sell_task->currency_one=$task->currency_one;
                    $sell_task->currency_two=$task->currency_two;

                    $sell_task->start_rate=$trading_pair->statistics[0]->bid;
                    $sell_task->rate=$trading_pair->statistics[0]->ask*0.9;

                    $sell_task->tokens_count=$task->tokens_count;
                    $sell_task->random_curve=0;
                    $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                    $sell_task->progress=0;
                    $sell_task->time=time();
                    $sell_task->is_user=1;

                    //try to set campaign
                    if($task->campaign_id)
                        $sell_task->campaign_id=$task->campaign_id;

                    $sell_task->save();
                    print_r($sell_task->errors);

                    $sell_task->make2(
                        Currency::find()->where(['symbol'=>$sell_task->currency_one])->limit(1)->one()->id,
                        Currency::find()->where(['symbol'=>$sell_task->currency_two])->limit(1)->one()->id);
                }
            }

        }
    }
}
