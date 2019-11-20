<?php
namespace backend\controllers;

use common\components\ApiRequest;
use common\models\Currency;
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

class CurrencyController extends Controller
{
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
	public function actionView($id) {
		
		$currency = Currency::findOne($id);
		
		if(Yii::$app->request->isPost) {
		    $errors=[];
            //$currency->load($_POST);
            if(empty($currency)){
                $currency=new Currency();
                $currency->created_at=time();
            }
            $currency['name']=$_POST['name'];
            $currency['symbol']=$_POST['symbol'];
            $currency['decimals']=intval($_POST['decimals']);
            $currency['type']=$_POST['type'];
            $currency['address']=$_POST['address'];
            $currency['data']=$_POST['data'];
            $currency->save();

            if($currency->errors){
                print_r($currency->errors);
                return $currency->errors;
            }else{
                $currencyArray=ArrayHelper::toArray($currency);

                $result = ApiRequest::statistics('v1/currency/add',$currencyArray);
                if($result->status!=1)
                    $errors[]=$result;

                $result = ApiRequest::accounts('v1/currency/add',$currencyArray);
                if($result->status!=1)
                    $errors[]=$result;

                Yii::$app->session->setFlash('log', $errors);
                return $this->redirect('/currency');
            }
		}
        //return 1;
		
		return $this->render("view", ['currency' => $currency]);
	}

	public function actionIndex(){
	    $currencies=Currency::find()->all();
	    $max_id=Currency::find()->select("max(id) id")->one();

	    $currency_new_id=$max_id->id+1;

        return $this->render("index", ['currencies' => $currencies,'currency_new_id'=>$currency_new_id]);
    }
}
