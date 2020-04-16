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

		$accounts=ApiRequest::statistics('v1/account/list',['limit'=>2000]);
        $accounts=ArrayHelper::toArray($accounts->data);
        foreach ($accounts as &$account){
            $account['balances']=json_decode($account['balances'],true);
        }

		        $m=[];
        foreach ( $col as $key=>$b ) {
            $m[]= $markets[$key]    ;
        }
        $m=array_reverse($m);

        return $this->render('index', ['markets'=>$m,'accounts'=>$accounts]);
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
    public function actionReleases(){
        $markets=Market::find()->all();

        $markets_remaped=[];
        foreach ($markets as $market){
            $markets_remaped[$market->id]=$market;
        }
        if(isset($get['percent_drop']))
            $percent_drop=$get['percent_drop'];
        else
            $percent_drop='0.006';

        if(isset($get['date_start']))
            $date_start=$get['date_start'];
        else
            $date_start='2019-01-01';

        $date_end=date('Y-m-d', strtotime($date_start . ' +1 month'));


        if(isset($get['percent_bounce']))
            $percent_bounce=$get['percent_bounce'];
        else
            $percent_bounce='0.001';

        if(isset($get['percent_profit']))
            $percent_profit=$get['percent_profit'];
        else
            $percent_profit='0.004';
        if(isset($get['timeout']))
            $timeout=$get['timeout'];
        else
            $timeout=3*24*3600;
        $forecasts=ApiRequest::statistics('v1/forecast/list',['limit'=>100]);
        $fakes=ApiRequest::statistics('v1/orders/fake',['limit'=>100]);

        $data=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>"BTCUSDT",'date_start'=>'2020-01-01','date_end'=>date("Y-m-d",time()),'timeframe'=>'1d']);
        $data=json_decode(json_encode($data->data),true);

        $forecast_statistics=ApiRequest::statistics('v1/forecast/statistics-day',[]);


        return $this->render('releases', [
            'orders'=>$fakes->data,
            'pair'=>"BTCUSDT",
            'percent_drop'=>$percent_drop,
            'percent_bounce'=>$percent_bounce,
            'percent_profit'=>$percent_profit,
            'timeout'=>$timeout,
            'date_start'=>$date_start,
            'date_end'=>$date_end,
            'markets'=>$markets_remaped,
            'forecasts'=>$forecasts->data,
            'data'=>$data,
            'forecast_statistics'=>$forecast_statistics->data
        ]);
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
}
