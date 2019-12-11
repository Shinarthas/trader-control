<?php
namespace backend\controllers;

use Codeception\Template\Api;
use common\assets\Hitbtc\Model\Order;
use common\components\ApiRequest;
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

class PossibilityController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

	public function actionIndex(){
	    $possibilityTable=ApiRequest::statistics('v1/possibility/current',[]);

	    $promotions_ids=[];
	    foreach ($possibilityTable->data as $promotion){
            $promotions_ids[]=$promotion->id;
        }

	    $orders=Task::find()->andWhere(['in', 'promotion_id',$promotions_ids])->orderBy('id desc')->limit(20)->all();
	    //print_r(ArrayHelper::toArray($orders));
        return $this->render("index", ['possibility' => $possibilityTable->data,'orders'=>$orders]);
    }
}
