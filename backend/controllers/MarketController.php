<?php
namespace backend\controllers;

use common\components\ApiRequest;
use common\models\Account;
use common\models\Task;
use function React\Promise\all;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Market;

/**
 * Site controller
 */
class MarketController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    public function actionView($id)
    {
		$market = Market::find()->where(['id'=>$id])->limit(1)->one();
		$accounts=Account::find()->where(['type'=>$market->id])->all();
		$accounts=ArrayHelper::toArray($accounts);
		foreach ($accounts as &$account){

            $date=time();
            for($i=$date-12*3600;$i<=$date+10;$i+=3600){
                $account['balances'][]= ApiRequest::statistics('v1/account/get-balance-time', ['id'=>$account['id'],'timestamp'=>$i]);
            }
        }
		$acc_ids=[];
		foreach ($accounts as $a)
            $acc_ids[]=$a['id'];
		print_r($acc_ids);
		$orders=Task::find()->orderBy('id desc')
            ->andWhere(['not in','status',[1,3]])
            ->andWhere(['in','account_id',$acc_ids])
            ->limit(100)->all();
		//print_r(ArrayHelper::toArray())
		
        return $this->render('view', ['market'=>$market,'accounts'=>$accounts,'orders'=>$orders]);
    }
	
	public function actionCampaign() {
		return $this->render("campaign");
	}
}
