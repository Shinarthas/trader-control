<?php
namespace backend\controllers;

use Codeception\Template\Api;
use common\assets\Hitbtc\Model\Order;
use common\components\ApiRequest;
use common\models\Company;
use common\models\DemoBalance;
use common\models\DemoTask;
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

class Trader2Controller extends Controller
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
	    $trading_pairs=ApiRequest::statistics('v1/trader2/list',[]);
        $trading_pairs=$trading_pairs->data;

        $balances=DemoBalance::find()->limit(1000)->orderBy('id desc')->all();

        $orders=DemoTask::find()->orderBy('id desc')->limit(50)->all();

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp;
        }
        $trading_pairs_remapped=[];
        foreach ($trading_pairs as $trading_pair){
            $trading_pairs_remapped[$trading_pair->trading_paid]=$trading_pair;
        }


	   $Companies=Company::find()->all();


        return $this->render("index", [
            'companies' => $Companies,
            'trading_pairs'=>$trading_pairs,
            'orders'=>$orders,
            'balances'=>$balances,
            'trading_pairs_remapped'=>$trading_pairs_remapped
        ]);
    }

    public function actionIndex2(){
        $trading_pairs=ApiRequest::statistics('v1/trader2/list',[]);
        $trading_pairs=$trading_pairs->data;

        $balances=DemoBalance::find()->limit(30)->orderBy('id desc')->all();

        $orders=DemoTask::find()->orderBy('id desc')->limit(300)->all();

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp;
        }
        $trading_pairs_remapped=[];
        foreach ($trading_pairs as $trading_pair){
            $trading_pairs_remapped[$trading_pair->trading_paid]=$trading_pair;
        }


        $Companies=Company::find()->all();


        return $this->render("index2", [
            'companies' => $Companies,
            'trading_pairs'=>$trading_pairs,
            'orders'=>$orders,
            'balances'=>$balances,
            'trading_pairs_remapped'=>$trading_pairs_remapped
        ]);
    }

    public function actionNew(){
	    if(Yii::$app->request->isPost){
	        if($this->update())
	            return $this->redirect('/trader2');
        }

	    return $this->render('edit');
    }

    public function actionEdit($id){
        if(Yii::$app->request->isPost){
            if($this->update())
                return $this->redirect('/trader2');
        }
	    $company=Company::findOne($id);
        return $this->render('edit',compact('company'));
    }

    public function update(){
        $post=Yii::$app->request->post();

        $company=Company::findOne($post['id']);
        unset($post['id']);
        if(empty($company)){
            $company=new Company();
            $company->created_at=date('Y-m-d H:i:s',time());
        }

        $company->attributes=$post;
        if($company->save())
            return 1;
        else{
            print_r($company->errors);
            return 0;
        }
    }
}
