<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
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
}