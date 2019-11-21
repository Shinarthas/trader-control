<?php

namespace api\v1\controllers;

use api\v1\renders\ResponseRender;
use api\v1\renders\OrderRender;

use Yii;
use api\v1\extensions\controllers\AuthApiController;
use common\models\Task;
use common\components\ApiRequest;

class SiteController extends AuthApiController
{
	public function actionIndex() {
        return ResponseRender::success(['msg'=>'im working']);
	}
}