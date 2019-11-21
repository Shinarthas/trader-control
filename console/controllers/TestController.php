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
	public function actionCancel(){
	    $task=Task::findOne(83);
	    $task->cancelOrder();
    }
}