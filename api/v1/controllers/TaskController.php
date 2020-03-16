<?php

namespace api\v1\controllers;

use api\v1\renders\ResponseRender;
use api\v1\renders\OrderRender;

use common\models\Campaign;
use common\models\Currency;
use common\models\Order;
use Yii;
use api\v1\extensions\controllers\AuthApiController;
use common\models\Task;
use common\components\ApiRequest;

class TaskController extends AuthApiController
{
	public function actionUpdate() {
		$order = Task::findOne($_POST['id']);

		if(empty($order)){
            $order=new Task();
            $order->random_curve=0;
            $order->value=$_POST['tokens_count']*$_POST['rate'];
            $order->time=$_POST['created_at'];
            $order->id=$_POST['id'];

            //нужно найти кому он пренадлежит;
            $campaigns=Campaign::find()->all();
            foreach ($campaigns as $campaign){
                if(in_array($_POST['account_id'],$campaign->accounts ))
                    $order->campaign_id=$campaign->id;
            }
            if(isset($_POST['currency_one']) && $currency_one=Currency::findOne($_POST['currency_one']))
                $order->currency_one=$currency_one->symbol;
            if(isset($_POST['currency_two']) && $currency_two=Currency::findOne($_POST['currency_two']))
                $order->currency_two=$currency_two->symbol;
        }else{
            $order->loaded_at = time();
        }
		$order->attributes = $_POST;

		if(!$order->save())
			return ResponseRender::failure(ResponseRender::VALIDATION_ERROR, $order->errors);
			
		return ResponseRender::success(['order_id'=>$order->id]);
	}

    public function actionCreate() {
        $order = Task::findOne($_POST['id']);

        if(empty($order)){
            $order=new Task();
            $order->random_curve=0;
            $order->value=$_POST['tokens_count']*$_POST['rate'];
            $order->time=$_POST['created_at'];
            $order->id=$_POST['id'];

            //нужно найти кому он пренадлежит;
            $campaigns=Campaign::find()->all();
            foreach ($campaigns as $campaign){
                if(in_array($_POST['account_id'],$campaign->accounts ))
                    $order->campaign_id=$campaign->id;
            }
            if(isset($_POST['currency_one']) && $currency_one=Currency::findOne($_POST['currency_one']))
                $order->currency_one=$currency_one->symbol;
            if(isset($_POST['currency_two']) && $currency_two=Currency::findOne($_POST['currency_two']))
                $order->currency_two=$currency_two->symbol;
        }else{
            $order->loaded_at = time();
        }
        $order->attributes = $_POST;

        if(!$order->save())
            return ResponseRender::failure(ResponseRender::VALIDATION_ERROR, $order->errors);

        return ResponseRender::success(['order_id'=>$order->id]);
    }
	public function actionGetMax(){
	    $task=Task::find()->orderBy('id desc')->limit(1)->one();
	    return ResponseRender::success(['id'=>!empty($task->id)?$task->id:0]);
    }
}