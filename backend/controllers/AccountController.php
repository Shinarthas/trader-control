<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Account;
use common\components\ApiRequest;

class AccountController extends Controller
{
    public static $account_types=[
        ['name'=>'TRX Account','id'=>1],
        ['name'=>'Tron Trade Account','id'=>2],
        ['name'=>'Binance Account','id'=>3,'json_fields'=>['api_key','secret']],
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
			$a->label = $_POST['label'];
			$a->save();
			//return $this->redirect("/account/");
		}
//		return $this->render("add",
//            [
//                'account_types'=>self::$account_types
//            ]);
	}
}
