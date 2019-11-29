<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use common\assets\BikiApi;
use common\components\ApiRequest;
use common\models\Account;
use common\models\CurrencyPrice;
use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Task;
use common\models\Promotion;

class TestController extends Controller
{
	public function actionCancel($id){
	    $task=Task::findOne($id);
	    $task->cancelOrder();
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
	    $res=ApiRequest::statistics('v1/promotion/graph',['promotion_id'=>5,'candles'=>24]);
	    print_r($res);
    }

    public function actionPromotion($task_id){
	    $task=Task::findOne($task_id);
	    print_r($task->promotion);
    }
}