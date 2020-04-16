<?php
namespace backend\controllers;

use common\models\Account;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

use common\models\Market;
use common\models\Task;
use common\models\CurrencyPrice;

use common\components\ApiRequest;

/**
 * Site controller
 */
class SiteController extends Controller
{

	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest AND $this->action->id!="login") {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
		$markets = Market::find()->all();
		foreach ($markets as &$market){
		    $res=ApiRequest::statistics('v1/market/get-statistics',['id'=>$market->id]);
		    $market->statistics=$res->data;
		    //print_r($res->data);
        }
		$col=[];
		foreach ($markets as &$market ){
            $col[]= $market->statistics->now->usdt_balance;
        }
		asort($col);
		$accounts=Account::find()->all();
        $accounts=ArrayHelper::toArray($accounts);
		foreach ($accounts as &$account){
            $account['balance']= ApiRequest::statistics('v1/account/get-balance-time', ['id'=>$account['id'],'timestamp'=>time()-120]);
        }

        $m=[];
        foreach ( $col as $key=>$b ) {
            $m[]= $markets[$key]    ;
        }
        $m=array_reverse($m);

        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['includes'=>'USDT','limit'=>50]);
        $trading_pairs_remaped=[];
        foreach ($trading_pairs->data as $trading_pair){
            $trading_pairs_remaped[$trading_pair->trading_paid]=$trading_pair;
        }


        return $this->render('index', ['markets'=>$m,'accounts'=>$accounts,'trading_pairs'=>$trading_pairs_remaped]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
	
		$this->layout = "simple";
		
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
	
	public function actionApiChecker() {
		$responce = '';
	
		if(isset($_POST['go']))
			$responce = ApiRequest::request($_POST['server'], $_POST['action'], json_decode($_POST['data']));
		
		return $this->render("api-checker", ['responce'=>$responce]);
	}
	
	public function actionTest() {

	$buy_task = Task::find()->where(['sell'=>0])->limit(1)->one();
	$sell_task = Task::find()->where(['sell'=>1])->limit(1)->one();
	
	echo "<table>";
	
		for($i = 5;$i<100;$i++) {
		echo "<tr>";
			$c_p = new CurrencyPrice;
			$c_p->currency_one = 1;
			$c_p->currency_two = 5;
			$c_p->market_id = 1;
			$c_p->buy_price = $i;
			$c_p->sell_price = $i+1;
			$c_p->created_at = time()-1;
			$c_p->save();
			
			$buy_task->calculateRate();
			$sell_task->calculateRate();
			
			echo "<td>".$c_p->buy_price."</td><td>".$c_p->sell_price."</td><td>".$buy_task->rate."</td><td>".$sell_task->rate."</td>";
			
		echo "</tr>";
		}
		
	echo "</table>";
		exit();
	//	return $this->render("test");
	}
	
	public function actionStatistic() {
		return $this->render("statistic");
	}
}
