<?php
namespace backend\controllers;

use Codeception\Template\Api;
use common\assets\Hitbtc\Model\Order;
use common\components\ApiRequest;
use common\models\Market;
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

class ReportsController extends Controller
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
	    $date_start=isset($_GET['date_start'])?$_GET['date_start']:date('Y-m-d',strtotime("-2 day"));
	    $date_end=isset($_GET['date_end'])?$_GET['date_end']:date('Y-m-d',strtotime("-1 day"));



	    $fakes=ApiRequest::statistics('v1/orders/fake',['date_start'=>$date_start,'date_end'=>$date_end]);
	    //print_r(ArrayHelper::toArray($fakes));
	    //die();
        $markets=Market::find()->all();

        $markets_remaped=[];
        foreach ($markets as $market){
            $markets_remaped[$market->id]=$market;
        }
        return $this->render("index",[
            'date_start'=>$date_start,
            'date_end'=>$date_end,
            'orders'=>$fakes->data,
            'markets'=>$markets_remaped
        ]);
    }
    public function  actionMarkets(){
	    $pairs=ApiRequest::statistics('v1/trader2/list',['limit'=>500,'includes'=>'BTC','last'=>'BTC']);
	    $predictions=ApiRequest::statistics('v1/trader2/predictions',['limit'=>500,'includes'=>'BTC','last'=>'BTC']);

        return $this->render("market",['pairs'=>$pairs->data,'predictions_backend'=>$predictions->data]);
    }
    public function actionMarketUpdate(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pairs=ApiRequest::statistics('v1/trader2/list',['limit'=>500,'includes'=>'BTC','last'=>'BTC']);
        return $pairs->data;
    }

    public function actionPredictionUpdate(){
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $pairs=ApiRequest::statistics('v1/trader2/predictions',['last'=>'BTC']);
        return $pairs->data;
    }
    public function actionPrediction(){
	    $symbols_info=ApiRequest::statistics('v1/trader2/list',['last'=>'BTC']);
        $remaped=[];
        foreach ($symbols_info->data as $trading_pair){
            $remaped[$trading_pair->trading_paid]=$trading_pair;
        }

	    $data=ApiRequest::statistics('v1/trader2/prediction-list',[]);
        $statistics=[];

	    $time=time();



	    foreach ($data->data as $stat){
	        if(strtotime($stat->timestamp_start)<$time-7*24*3600)
	            continue;
	        foreach ($stat->ids as $symbol=>$result){
	            echo $remaped[$symbol]->type;
	            if(strpos($symbol,'BTC')<2)
	                continue;
                if(!$statistics[$symbol]) {
                    $statistics[$symbol] = [
                        'hour' => [
                            'count' => 0,
                            'successful' => 0,
                            'percent' => 0,
                        ],
                        'day' => [
                            'count' => 0,
                            'successful' => 0,
                            'percent' => 0,
                        ],
                        'week' => [
                            'count' => 0,
                            'successful' => 0,
                            'percent' => 0,
                        ],
                        'currency_group'=>isset($remaped[$symbol])?$remaped[$symbol]->currency_group:0
                    ];
                }

                if(strtotime($stat->timestamp_start)>$time-3600){
                    $statistics[$symbol]['hour']['count']++;
                    if($result>0)
                        $statistics[$symbol]['hour']['successful']++;
                    $statistics[$symbol]['hour']['percent']+=$result;
                }
                if(strtotime($stat->timestamp_start)>$time-24*3600){
                    $statistics[$symbol]['day']['count']++;
                    if($result>0)
                        $statistics[$symbol]['day']['successful']++;
                    $statistics[$symbol]['day']['percent']+=$result;
                }
                if(strtotime($stat->timestamp_start)>$time-7*24*3600){
                    $statistics[$symbol]['week']['count']++;
                    if($result>0)
                        $statistics[$symbol]['week']['successful']++;
                    $statistics[$symbol]['week']['percent']+=$result;
                }
            }


        }

	    return $this->render('prediction',['statistics'=>$statistics]);
    }

    public function actionPair($symbol){
	    $cmc_data=ApiRequest::statistics('v1/coin-market-cap/info',[
	        'symbol'=>str_replace('BTC','',$symbol)
        ]);
        $prediction=ApiRequest::statistics('v1/trader2/predictions',[]);

        $full_info=ApiRequest::statistics('v1/trader2/symbol-prediction-info',
            ['symbol'=>$symbol]);

	    return $this->render('symbol',[
	        'symbol'=>$symbol,
            'cmc_coin'=>$cmc_data->data,
            'prediction'=>$prediction,
            'pairs'=>$full_info->data
        ]);
    }
}