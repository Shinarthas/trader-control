<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
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
	    $promotion->checkPrice();
    }
    public function actionPriceNow($promotion_id){
	    $promotion=Promotion::findOne($promotion_id);
	    $res=CurrencyPrice::currentPrice($promotion->market_id,$promotion->currency_one,$promotion->currency_two);
	    print_r($res);
    }
}