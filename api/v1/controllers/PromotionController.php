<?php

namespace api\v1\controllers;

use api\v1\renders\ResponseRender;
use api\v1\renders\OrderRender;

use common\models\PromotionAccount;
use Yii;
use api\v1\extensions\controllers\AuthApiController;
use common\models\Task;
use common\components\ApiRequest;

class PromotionController extends AuthApiController
{
	public function actionGetWiredAccounts(){
	    $promotion_id=Yii::$app->request->post('id');

	    $accounts=PromotionAccount::find()->select('account_id')
            ->where(['promotion_id'=>$promotion_id])->all();
	    return ResponseRender::success($accounts);

    }
}