<?php
namespace console\controllers;

use api\v1\renders\ResponseRender;
use backend\assets\DepthAnalizer;
use common\assets\BikiApi;
use common\assets\CoinMarketCapApi;
use common\components\ApiRequest;
use common\models\Account;
use common\models\Campaign;
use common\models\Currency;
use common\models\CurrencyPrice;
use common\models\Trading;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Task;
use common\models\Promotion;

class TestController extends Controller
{
	public function actionCancel($id){
	    $task=Task::findOne($id);
	    $res=$task->cancelOrder();
	    print_r($res);
    }

    public function actionPrice(){
	    $promotion=Promotion::findOne(5);
	    print_r($promotion->checkPrice());
    }
    public function actionPriceNow($promotion_id){
	    $promotion=Promotion::findOne($promotion_id);
	    $res=CurrencyPrice::currentPrice($promotion->market_id,$promotion->currency_one,$promotion->currency_two,900,600);
	    print_r($res);
    }
    public function actionBalance($account_id){
	    $account=Account::findOne($account_id);
	    $res=$account->getBalance();
	    print_r($res);
    }
    public function actionLogList(){
	    $res=ApiRequest::statistics('v1/log/get',[]);
	    print_r($res);
    }
    public function actionBiki(){
	    $biki=new BikiApi();
	    $res=$biki->depth(strtolower('ethbtc'));
	    print_r($res);
    }
    public function actionPeriod(){
        //$biki=new BikiApi();
        $res=Trading::getPeriod();
        print_r($res);
    }
    public function actionGetUsdt(){
	    Trading::getUsdtWithBtc();
    }
    public function actionGetBtc(){
        Trading::getBtcWithUsdt();
    }
    public function actionCmc(){
	    CoinMarketCapApi::top();
    }
    public function actionGraph(){
	    $data=[
	        'id'=>"TRX",
            'timeframe'=>60,
            'date_from'=>'30.10.2019',
            'date_to'=>'30.12.2019',
            'promotion_id'=>5
        ];
	    $res=ApiRequest::statistics('v1/promotion/graph',$data);
	    print_r($res);
    }

    public function actionPromotion($task_id){
	    $task=Task::findOne($task_id);
	    print_r($task->promotion);
    }

    public function actionCalculateAccount(){
	    $promotoin=Promotion::findOne(8);
	    $account=$promotoin->calculateAccount(1,0.1);
	    print_r(ArrayHelper::toArray($account));
    }
    public function actionDepth(){
	    $res=DepthAnalizer::getPossibility('BTC');
	    print_r($res);
    }
    public function actionCampaign(){
        $campaign=Campaign::findOne(1);
        $campaign->index();
    }
    public function actionCampaignToUsdt(){
        $campaign=Campaign::findOne(1);
        $campaign->getUsdtWithEntrance();
    }
    public function actionAllWithUsdt(){
        $campaign=Campaign::findOne(1);
        $campaign->getUsdtWithAll();
    }
    public function actionCampaignBalance(){
        $campaign=Campaign::findOne(1);
        $res=$campaign->getBalance();

        print_r($res);
    }
    public function actionCampaignGetEntrance(){
        $campaign=Campaign::findOne(1);
        $campaign->getEntranceWithUsdt();
    }

    public function actionCampaignCloseOutdated(){
        $campaign=Campaign::findOne(1);
        $campaign->closeOutdated();
    }

    public function actionMake2(){
        $task=Task::findOne(790);
        $task->make2(5,6);
    }
    public function actionError(){
	    return 10/0;
    }
    public function actionUsdtRates(){
	    print_r(Task::getCurrencyToUsdt());
    }
    public function actionFix(){
        $pairs=ApiRequest::statistics('v1/trader2/list',['limit'=>500,'includes'=>'BTC','last'=>'BTC']);
        $pairs=$pairs->data;
        foreach ($pairs as $trading_pair){
            $symbol=str_replace('BTC','',$trading_pair->trading_paid);
            $currency=Currency::find()->where(['symbol'=>$symbol])->limit(1)->one();
            if(empty($currency)){
                $currency=new Currency();
                $currency->created_at=time();

                $currency['name']=$symbol;
                $currency['symbol']=$symbol;
                $currency['decimals']=6;
                $currency['type']=1;
                $currency['address']='0x';
                $currency['class']='GoodCurrency';
                $currency['data']=[];
                $currency->save();
                if(!$currency->errors){
                    $currencyArray=ArrayHelper::toArray($currency);
                    $result = ApiRequest::statistics('v1/currency/add',$currencyArray);
                    if($result->status!=1)
                        $errors[]=$result;

                    $result = ApiRequest::accounts('v1/currency/add',$currencyArray);
                    if($result->status!=1)
                        $errors[]=$result;
                }
            }
        }
    }

}