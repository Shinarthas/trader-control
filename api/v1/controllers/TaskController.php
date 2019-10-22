<?php

namespace api\v1\controllers;

use api\v1\renders\ResponseRender;
use api\v1\renders\OrderRender;

use Yii;
use api\v1\extensions\controllers\AuthApiController;
use common\models\Task;
use common\components\ApiRequest;

class TaskController extends AuthApiController
{
	public function actionUpdate() {
		$order = Task::findOne($_POST['id']);
		$order->attributes = $_POST;
		$order->loaded_at = time();
		if(!$order->save())
			return ResponseRender::failure(ResponseRender::VALIDATION_ERROR, $order->errors);
			
		return ResponseRender::success(['order_id'=>$order->id]);
	}
}