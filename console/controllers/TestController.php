<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use common\assets\BikiApi;
use common\components\ApiRequest;
use common\models\Account;
use common\models\CurrencyPrice;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Task;
use common\models\Promotion;

class TestController extends Controller
{
	public function actionCancel($id){
	    $task=Task::findOne($id);
	    $res=$task->cancelOrder();
	    print_r($res);
    }

    public function actionPrice(){
	    $promotion=Promotion::findOne(5);
	    print_r($promotion->checkPrice());
    }
    public function actionPriceNow($promotion_id){
	    $promotion=Promotion::findOne($promotion_id);
	    $res=CurrencyPrice::currentPrice($promotion->market_id,$promotion->currency_one,$promotion->currency_two,900,600);
	    print_r($res);
    }
    public function actionBalance($account_id){
	    $account=Account::findOne($account_id);
	    $res=$account->getBalance();
	    print_r($res);
    }
    public function actionLogList(){
	    $res=ApiRequest::statistics('v1/log/get',[]);
	    print_r($res);
    }
    public function actionBiki(){
	    $biki=new BikiApi();
	    $res=$biki->depth(strtolower('ethbtc'));
	    print_r($res);
    }
    public function actionGraph(){
	    $data=[
	        'id'=>"TRX",
            'timeframe'=>60,
            'date_from'=>'30.10.2019',
            'date_to'=>'30.12.2019',
            'promotion_id'=>5
        ];
	    $res=ApiRequest::statistics('v1/promotion/graph',$data);
	    print_r($res);
    }

    public function actionPromotion($task_id){
	    $task=Task::findOne($task_id);
	    print_r($task->promotion);
    }

    public function actionCalculateAccount(){
	    $promotoin=Promotion::findOne(8);
	    $account=$promotoin->calculateAccount(1,0.1);
	    print_r(ArrayHelper::toArray($account));
    }
}