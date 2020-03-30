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
        $accounts=ApiRequest::statistics('v1/account/list',['type'=>$market->id,'limit'=>2000]);
        $accounts=ArrayHelper::toArray($accounts->data);
        foreach ($accounts as &$account){
            $account['balances']=json_decode($account['balances'],true);
        }
		$acc_ids=[];
		foreach ($accounts as $a)
            $acc_ids[]=$a['id'];
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
