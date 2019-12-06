<?php
namespace backend\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use common\models\Account;
use common\components\ApiRequest;

class AccountController extends Controller
{
    public static $account_types=[
        ['name'=>'TRX Account','id'=>1],
        ['name'=>'Tron Trade Account','id'=>2],
        ['name'=>'Binance Account','id'=>3,'json_fields'=>['api_key','secret']],
        ['name'=>'Poloniex Account','id'=>5,'json_fields'=>['api_key','secret']],
        ['name'=>'HitBTC Account','id'=>6,'json_fields'=>['api_key','secret']],
    ];
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}

    public function actionIndex()
    {
		$accounts = Account::find()->limit(50)->all();

        return $this->render('index', ['accounts' => $accounts]);
    }
	
	public function actionAdd() {
		if(isset($_POST['add'])) {
			$res = ApiRequest::accounts('v1/account/create', $_POST);
			print_r($res);
			$a = new Account;
			$a->id = $res->data->account_id;
			$a->name = $_POST['name'];
			$a->type = $_POST['type'];
			$a->check_balance = $_POST['check_balance'];
			$a->label = $_POST['label'];
			$a->save();
			return $this->redirect("/account/");
		}
		return $this->render("add",
            [
                'account_types'=>self::$account_types
            ]);
	}
	
	public function actionView($id) {
		$a = Account::findOne($id);
		if(Yii::$app->request->isPost){
            $res = ApiRequest::accounts('v1/account/update', $_POST);
            $a2=Account::findOne( $res->data->account_id);

            $a2->name = $_POST['name'];
            $a2->check_balance = $_POST['check_balance'];
            $a2->label = $_POST['label'];
            $a2->save();
            print_r($res);
            print_r($a2->errors);
            if(empty($a2->errors))
                $a=$a2;
        }

		return $this->render("view", ['a'=>$a,'account_types'=>self::$account_types]);
	}
}
