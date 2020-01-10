<?php

namespace common\models;

use common\components\ApiRequest;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property array $settings
 * @property array $accounts
 * @property double $trigger_score
 * @property double $maximal_stake
 * @property array $strategy
 * @property int $timeout
 * @property string $created_at
 * @property string $entrance_currency
 */
class Campaign extends \yii\db\ActiveRecord
{
    public $trading_pairs=[];
    public $entrance_usdt=[];
    public $balances=[];
    public $current_trigger_score=0;
    public $current_usdt_volume=0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settings', 'strategy', 'created_at','accounts','name','entrance_currency'], 'safe'],
            [['trigger_score', 'maximal_stake'], 'number'],
            [['timeout'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settings' => 'Settings',// настройки для опр шанса
            'trigger_score' => 'Trigger Score',// граничное значение шанса при котором запустить сделку
            'maximal_stake' => 'Maximal Stake',// минимальный обьем от банка на бирже
            'strategy' => 'Strategy',
            'timeout' => 'Timeout',
            'accounts' => 'Accounts',
            'entrance_currency' => 'entrance_currency',
            'name' => 'name',
            'created_at' => 'Created At',
        ];
    }
    private function trade(){
        if(empty($this->balances))
            $this->getBalance();

        //сколько у нас той валюты через которую хотим заходить
        $entrance_value=0;

        foreach ($this->balances as $account_id=>$balances){


            foreach ($balances->balances as $balance){
                if($balance->name==$this->entrance_currency)
                    $entrance_value+=$this->entrance_usdt->{'now'}->bid;
            }
        }
        //начинаем торговать
        foreach ($this->trading_pairs as $trading_pair){
            //тут должен ббыть функионал под каждый параметр
            $random_per=mt_rand() / mt_getrandmax()*$this->maximal_stake/100;
            //если эта монета вообще торгуется
            if($trading_pair->statistics->{'now'}->bid!=0){
                $local_symbol=str_replace($this->entrance_currency,'',$trading_pair->trading_paid);
                $this->current_trigger_score=$this->getTriggerScore($local_symbol);
                //if(true && $entrance_value>=$this->current_usdt_volume*$random_per){
                if($this->current_trigger_score>=$this->trigger_score && $entrance_value>=$this->current_usdt_volume*$random_per){
                    //найдем наши валюты
                    $currency_one=Currency::find()->where(['symbol'=>$local_symbol])->limit(1)->one();
                    $currency_two=Currency::find()->where(['symbol'=>$this->entrance_currency])->limit(1)->one();
                    if(empty($currency_one) ||  empty($currency_two)){
                        Log::log(['msg'=>'нет валюты '.$local_symbol.' или '.$this->entrance_currency]);
                        continue;//какой-то валюты нет
                    }
                    //закупим эту валюту валюту если не хватает
                    $tokens_count_on_accounts=[];//надо чтоб точно знать как все прошло
                    foreach ($this->accounts as $account){
                        if(!$this->weNeedToPlaceBuyOrder($account,$random_per,$local_symbol,$trading_pair->statistics->{'now'}->bid)){
                            echo "no need to buy" ."$local_symbol";
                            continue;
                        }

                        $buy_task=new Task();
                        $buy_task->account_id = $account;
                        $buy_task->promotion_id=0;
                        $buy_task->status=0;
                        $buy_task->sell=0;
                        $buy_task->rate=$trading_pair->statistics->{'now'}->bid*1.005;//чтоб точно купить
                        $buy_task->tokens_count=
                            $this->current_usdt_volume*$random_per/
                            $this->entrance_usdt->{'now'}->bid/
                            $trading_pair->statistics->{'now'}->bid;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                        $buy_task->random_curve=0;
                        $buy_task->value=($buy_task->tokens_count*$buy_task->rate);
                        $buy_task->progress=0;
                        $buy_task->time=time();
                        if($buy_task->tokens_count<0.00001)//у нас просто нет этой валюты, нас непропустят
                            continue;
                        $is_continue=0;//тригер для пропуска ставки если у нас нет денег
                        foreach ($this->balances[$account]->balances as $balance) {
                            if(!in_array($this->entrance_currency, //это значит у нас ее вообще нет на балансе
                                array_column(json_decode(json_encode($this->balances[$account]->balances),true),'name'))){

                                echo " no currency at all ";
                                $is_continue = 0;
                                break;
                            }
                            if ($balance->name == $this->entrance_currency) {
                                if ($buy_task->tokens_count > $balance->value/$trading_pair->statistics->{'now'}->bid){//у нас просто нет столько входной валюты
                                    $is_continue = 1;
                                    echo "---------".$buy_task->tokens_count." ".($balance->value/$trading_pair->statistics->{'now'}->bid)." $local_symbol ------"."\n";
                                }
                            }
                        }
                        if ($is_continue){
                            echo "  skip3  ";
                            continue;
                        }
                        foreach ($this->balances[$account]->balances as $balance){
                            if($balance->name==$this->entrance_currency){
                                $balance->value-=$buy_task->tokens_count*$buy_task->rate;
                            }
                            if($balance->name==$local_symbol){
                                $balance->value+=$buy_task->tokens_count;
                            }

                        }

                        $buy_task->save();
                        echo "-BUY-";
                        $buy_task->make2($currency_one->id,$currency_two->id);
                    }
                    //die();

                    sleep(1);//подождем пока на биржах  все пройдет

                    foreach ($this->accounts as $account){
                        $sell_task=new Task();
                        $sell_task->account_id = $account;
                        $sell_task->promotion_id=0;
                        $sell_task->status=0;
                        $sell_task->sell=1;
                        $sell_task->rate=$trading_pair->statistics->{'now'}->ask*1.004;//тут коеф профита
                        $tokens_count=0.01;
                        foreach ($this->balances[$account]->balances as $balance){
                            if($balance->name==$local_symbol){
                                $tokens_count=$balance->value;
                            }
                        }
                        if($tokens_count<0.000001){
                            echo 'skip';
                            continue;
                        }

                        $sell_task->tokens_count=$tokens_count*0.98;
                        $sell_task->random_curve=0;
                        $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                        $sell_task->progress=0;
                        $sell_task->time=time();
                        $is_continue=0;//тригер для пропуска ставки если у нас нет денег
                        foreach ($this->balances[$account]->balances as $balance) {
                            if(!in_array($local_symbol, //это значит у нас ее вообще нет на балансе
                                array_column(json_decode(json_encode($this->balances[$account]->balances),true),'name'))){
                                echo " no currency at all ";
                                $is_continue = 0;
                                break;
                            }
                            if ($balance->name == $local_symbol) {
                                if ($sell_task->tokens_count > $balance->value)//у нас просто нет столько входной валюты
                                    $is_continue = 1;
                            }
                        }
                        if ($is_continue){
                            echo "  skip2  ";
                            continue;
                        }

                        foreach ($this->balances[$account]->balances as $balance){
                            if($balance->name==$this->entrance_currency){
                                $balance->value+=$sell_task->tokens_count*$sell_task->rate;
                            }
                            if($balance->name==$local_symbol){
                                $balance->value-=$sell_task->tokens_count;
                            }

                        }

                        $sell_task->save();

                        $sell_task->make2($currency_one->id,$currency_two->id);
                    }
                    $this->getBalance();

                }
            }
        }
    }
    public function closeOutdated(){//добавить id  кампании
        $tasks=Task::find()->where(['promotion_id'=>0])
        //->andWhere(['not in','status',[0,4,5]])->andWhere(['<','time',time()-$this->timeout])
        ->andWhere(['not in','status',[0,1,4,5]])->andWhere(['>','time',1])
        ->all();

        foreach ($tasks as $task){
            $task->cancelOrder();
        }

        $this->getBalance();
    }
    public function index(){
        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'includes'=>$this->entrance_currency]);
        $trading_pairs=$trading_pairs->data;

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp->data;

        }
        //полумать что будет если USDTUSDT
        //получим входную  валюту
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;

        $this->trading_pairs=$trading_pairs;
        $this->entrance_usdt=$entrance_usdt;

        $this->trade();
    }
    public function getBalance(){
        $balances=[];
        $accounts=$this->accounts;
        $this->current_usdt_volume=0;
        foreach ($accounts as $account){
            $tmp= ApiRequest::statistics('v1/account/get-balance-now', ['id'=>$account]);
            $balances[$account]=$tmp->data;
            $this->current_usdt_volume+=$tmp->data->in_usd;
        }
        $this->balances=$balances;
        return $balances;
    }


    //тут наверное надо будет сделать процент
    public function getEntranceWithUsdt($save=0){
        if(empty($this->balances))
            $this->getBalance();
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;
        $this->entrance_usdt=$entrance_usdt;

        foreach ($this->balances as $account_id=>$balances){

            foreach ($balances->balances as $balance){
                if($balance->name=='USDT'){
//                    echo "$account_id ";
//                    print_r($balance);
                    $currency_one=Currency::find()->where(['symbol'=>$this->entrance_currency])->limit(1)->one();
                    $currency_two=Currency::find()->where(['symbol'=>'USDT'])->limit(1)->one();

                    foreach ($this->trading_pairs as $tp){
                        if($tp->trading_paid==$this->entrance_currency.'USDT'){
                            $trading_pair=$tp;
                            break;
                        }
                    }


                    $buy_task=new Task();
                    $buy_task->account_id = $account_id;
                    $buy_task->promotion_id=0;
                    $buy_task->status=0;
                    $buy_task->sell=0;
                    $buy_task->rate=$this->entrance_usdt->{'now'}->bid*1.005;//чтоб точно купить
                    $buy_task->tokens_count=$balance->value/$buy_task->rate*0.98;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                    $buy_task->random_curve=0;
                    $buy_task->value=($buy_task->tokens_count*$buy_task->rate);
                    $buy_task->progress=0;
                    $buy_task->time=time();
                    if($buy_task->tokens_count<0.001)
                        continue;
                    $buy_task->save();

                    $buy_task->make2($currency_one->id,$currency_two->id);
                }
            }
        }

        $this->getBalance();

    }
    //тут наверное надо будет сделать процент
    public function getUsdtWithEntrance($save=1000000){
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;
        $this->entrance_usdt=$entrance_usdt;

        if(empty($this->balances))
            $this->getBalance();

        foreach ($this->balances as $account_id=>$balances){
            foreach ($balances->balances as $balance){
                if($balance->name==$this->entrance_currency){
                    $currency_one=Currency::find()->where(['symbol'=>$this->entrance_currency])->limit(1)->one();
                    $currency_two=Currency::find()->where(['symbol'=>'USDT'])->limit(1)->one();

                    foreach ($this->trading_pairs as $tp){
                        if($tp->trading_paid==$this->entrance_currency.'USDT'){
                            $trading_pair=$tp;
                            break;
                        }
                    }

                    $sell_task=new Task();
                    $sell_task->account_id = $account_id;
                    $sell_task->promotion_id=0;
                    $sell_task->status=0;
                    $sell_task->sell=1;
                    $sell_task->rate=$trading_pair->statistics->{'now'}->ask*1.05;//чтоб точно купить
                    $sell_task->tokens_count=$balance->value;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                    $sell_task->random_curve=0;
                    $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                    $sell_task->progress=0;
                    $sell_task->time=time();
                    if($sell_task->tokens_count<0.001)
                        continue;
                    $sell_task->save();

                    $sell_task->make2($currency_one->id,$currency_two->id);
                }
            }
        }

        $this->getBalance();
    }
    public function getUsdtWithAll($save=1000000){
        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['includes'=>'USDT','limit'=>1000]);
        $trading_pairs=$trading_pairs->data;
//        print_r($trading_pairs);
//        die();
        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp->data;

        }
        //полумать что будет если USDTUSDT
        //получим входную  валюту
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;

        $this->trading_pairs=$trading_pairs;



        if(empty($this->balances))
            $this->getBalance();

        foreach ($this->balances as $account_id=>$balances){
            foreach ($balances->balances as $balance){
                if($balance->name=='USDT')//потому-что зачем менять usdt  на usdt ?
                    continue;
                $currency_one=Currency::find()->where(['symbol'=>$balance->name])->limit(1)->one();
                $currency_two=Currency::find()->where(['symbol'=>'USDT'])->limit(1)->one();

                $trading_pair=null;
                foreach ($this->trading_pairs as $tp){
                    if($tp->trading_paid==$balance->name.'USDT'){
                        $trading_pair=$tp;
                        break;
                    }
                }
                if(!isset($trading_pair) || empty($currency_one) || empty($currency_two)){
                    echo 'trading_pair not found('.$balance->name.'USDT'.') ';
                    continue;
                }

                $sell_task=new Task();
                $sell_task->account_id = $account_id;
                $sell_task->promotion_id=0;
                $sell_task->status=0;
                $sell_task->sell=1;
                $sell_task->rate=$trading_pair->statistics->{'now'}->ask*1.05;//чтоб точно купить
                $sell_task->tokens_count=$balance->value*0.98;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                $sell_task->random_curve=0;
                $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                $sell_task->progress=0;
                $sell_task->time=time();
//                echo '---'.$currency_one->symbol.' '.$currency_two->symbol." ";
//                print_r(ArrayHelper::toArray($sell_task));
//
//                continue;
                //echo $sell_task->tokens_count." ";
                if($sell_task->tokens_count<0.000000001){
                    $market=Market::findOne(Account::findOne($account_id)->type);
                    echo "---------------$market->name  ".$currency_one->symbol." ".$currency_two->symbol." $sell_task->tokens_count low amt ------------";
                    continue;
                }

                $sell_task->save();

                $sell_task->make2($currency_one->id,$currency_two->id);
            }

        }

        $this->getBalance();
    }

    public function check($stat){
        $executor=new Campaign();
    }
    public function getTriggerScore($symbol){
        $trigger_score=0;

        //тут мой код но его нужно потом завернуть в if  и переделать
        foreach ($this->trading_pairs as $tp){
            if($tp->trading_paid==$symbol.$this->entrance_currency){
                $trading_pair=$tp;
                break;
            }
        }
        $trigger_score+=self::findThresholdBreak(0.06,0.01,$trading_pair->statistics);


        return 100;
        return $trigger_score;
    }

    public static function findThresholdBreak($percent_drop,$percent_bounce,$statistics){
        $bid240=$statistics->{'240min'}->bid;
        $bid120=$statistics->{'120min'}->bid;
        $bid60=$statistics->{'60min'}->bid;
        $bid40=$statistics->{'40min'}->bid;
        $bid30=$statistics->{'30min'}->bid;
        $bid20=$statistics->{'20min'}->bid;
        $bid15=$statistics->{'15min'}->bid;
        $bid10=$statistics->{'10min'}->bid;
        $bid5=$statistics->{'5min'}->bid;
        $bid_now=$statistics->{'now'}->bid;

        $sequence=[$bid5,$bid10,$bid15,$bid20,$bid30,$bid40,$bid60,$bid120,$bid240];
        $count_sequence=count($sequence);
        for ($i=0;$i<$count_sequence;$i++){
            $time1=$sequence[$i];//всегда должна быть меньше чем время t2
            for ($j=$i+1;$j<$count_sequence;$j++){
                $time2=$sequence[$j];
                if(($time2-$time1)/$time1>$percent_drop && ($bid_now-$time1)/$time1>$percent_bounce )
                    return 100;
            }
        }
        return 0;

    }
    private function weNeedToPlaceBuyOrder($account,$random_per,$local_symbol,$exchange_price){
        //проверяем есть ли у нас столько монеты
        $we_have=0;
        $this->balances[$account]->balances;
        foreach ($this->balances[$account]->balances as $balance){
            if($balance->name==$local_symbol){
                if($balance->value*$exchange_price*$this->entrance_usdt->{'now'}->bid>=$random_per*$this->balances[$account]->in_usd)
                    $we_have=1;
            }
        }
        return !$we_have;
    }
}
