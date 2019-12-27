<?php
namespace backend\controllers;

use Codeception\Template\Api;
use common\assets\CoinMarketCapApi;
use common\assets\Hitbtc\Model\Order;
use common\components\ApiRequest;
use common\models\Campaign;
use common\models\Company;
use common\models\DemoBalance;
use common\models\DemoTask;
use common\models\Market;
use common\models\Trading;
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
	    $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'limit'=>10]);
        $trading_pairs=$trading_pairs->data;
        $period=Trading::getPeriod();
        $balances=DemoBalance::find()->limit(1000)->where(['period'=>$period])->orderBy('id desc')
            //->createCommand()->rawSql;
            ->all();
        //print_r($balances);

        $orders=DemoTask::find()->orderBy('id desc')->limit(50)->all();

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp;
        }
        $trading_pairs_remapped=[];
        foreach ($trading_pairs as $trading_pair){
            $trading_pairs_remapped[$trading_pair->trading_paid]=$trading_pair;
        }
        $top_currencies=[];
        $tc=CoinMarketCapApi::top();
        $tmp_rating=[];
        foreach ($tc->data as $key=>$cmc_currency){
            $tmp_rating[]=['key'=>$key,'rating'=>$cmc_currency->quote->USD->volume_24h*0.1*($cmc_currency->quote->USD->percent_change_1h)];
        }
        usort($tmp_rating, function($a, $b) {
            return ($a['rating'] <=> $b['rating']);
        });
        //print_r($tmp_rating);
        for($i=0;$i<6;$i++){
            $top_currencies[$i]=$tc->data[$tmp_rating[count($tmp_rating)-1-$i]['key']];
        }

        $markets=Market::find()->all();

	   $Companies=Campaign::find()->all();


        return $this->render("index", [
            'companies' => $Companies,
            'trading_pairs'=>$trading_pairs,
            'orders'=>$orders,
            'period'=>$period,
            'balances'=>$balances,
            'trading_pairs_remapped'=>$trading_pairs_remapped,
            'top_currencies'=>$top_currencies,
            'markets'=>$markets
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


        $Companies=Campaign::find()->all();


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
	    $company=Campaign::findOne($id);
        return $this->render('edit',compact('company'));
    }

    public function update(){
        $post=Yii::$app->request->post();

        $company=Campaign::findOne($post['id']);
        unset($post['id']);
        if(empty($company)){
            $company=new Campaign();
            $company->created_at=date('Y-m-d H:i:s',time());
        }
        print_r($post);
        $company->attributes=$post;
        if($company->save())
            return 1;
        else{
            print_r($company->errors);
            return 0;
        }
    }
    public function actionPairs(){
        if(Yii::$app->request->isPost){
            $res=ApiRequest::statistics('v1/trader2/update',Yii::$app->request->post());
            print_r($res);
        }

        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['limit'=>1000]);
        $trading_pairs=$trading_pairs->data;



        return $this->render("pairs", [
            'trading_pairs'=>$trading_pairs,

        ]);
    }

    public function actionPossibility(){
	    $get=Yii::$app->request->get();
	    if(isset($get['symbol']))
	        $symbol=$get['symbol'];
	    else
	        $symbol='ETHBTC';

        if(isset($get['percent_drop']))
            $percent_drop=$get['percent_drop'];
        else
            $percent_drop='0.006';


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
            $timeout=4*3600;

	    $data=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>$symbol]);
	    $data=json_decode(json_encode($data->data),true);

	    return $this->render("possibility", [
            'data'=>$data,
            'pair'=>$symbol,
            'percent_drop'=>$percent_drop,
            'percent_bounce'=>$percent_bounce,
            'percent_profit'=>$percent_profit,
            'timeout'=>$timeout,
        ]);
    }
}
