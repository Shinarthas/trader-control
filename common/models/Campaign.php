<?php

namespace common\models;

use backend\assets\DepthAnalizer;
use common\components\ApiRequest;
use phpDocumentor\Reflection\Types\Integer;
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
        $currency_to_usdt=ApiRequest::statistics('v1/currency/usdt-rates');
        $usdt_rates=[];
        foreach ($currency_to_usdt->data->rates as $usdt_rate){
            $usdt_rates[$usdt_rate->currency]=$usdt_rate;
        }

        //сколько у нас той валюты через которую хотим заходить
        $entrance_value=0;

        foreach ($this->balances as $account_id=>$balances){


            foreach ($balances->balances as $balance){
                if($balance->name==$this->entrance_currency)
                    $entrance_value+=$this->entrance_usdt[0]->bid;
            }
        }
        //начинаем торговать
        foreach ($this->trading_pairs as $trading_pair){
            //тут должен ббыть функионал под каждый параметр
            $random_per=mt_rand() / mt_getrandmax()*$this->maximal_stake/100;
            //если эта монета вообще торгуется
            if($trading_pair->statistics[0]->bid!=0){
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
                        if(!$this->weNeedToPlaceBuyOrder($account,$random_per,$local_symbol,$trading_pair->statistics[0]->bid)){
                            echo "no need to buy" ."$local_symbol";
                            continue;
                        }

                        $buy_task=new Task();
                        $buy_task->campaign_id = $this->id;
                        $buy_task->account_id = $account;
                        $buy_task->promotion_id=0;
                        $buy_task->status=0;
                        $buy_task->sell=0;
                        $buy_task->currency_one=$local_symbol;
                        $buy_task->currency_two=$this->entrance_currency;
                        $buy_task->rate=$trading_pair->statistics[0]->bid*1.05;//чтоб точно купить
                        $buy_task->start_rate=$trading_pair->statistics[0]->bid;//чтоб точно купить
                        $buy_task->tokens_count=
                            $this->current_usdt_volume*$random_per/
                            $this->entrance_usdt[0]->bid/
                            $trading_pair->statistics[0]->bid;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                        $buy_task->random_curve=0;
                        $buy_task->value=($buy_task->tokens_count*$buy_task->rate);
                        $buy_task->progress=0;
                        $buy_task->time=time();
                        //если тотал ордера меньше доллара то не допускать
                        if(isset($usdt_rates[$currency_one->id]) && $usdt_rates[$currency_one->id]->rate*$buy_task->tokens_count<1)
                            continue;

                        //если ставка меньше  $1.5, то не ставить ордер
                        if($buy_task->tokens_count*$buy_task->rate*$this->entrance_usdt[0]->bid<1.5)
                            continue;
                        echo 'usdt-->'.number_format($buy_task->tokens_count*$buy_task->rate*$this->entrance_usdt[0]->bid,2);
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
                                if ($buy_task->tokens_count > $balance->value/$trading_pair->statistics[0]->bid){//у нас просто нет столько входной валюты
                                    $is_continue = 1;
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

                    sleep(1);//подождем пока на биржах  все пройдет

                    foreach ($this->accounts as $account){
                        echo "sell";
                        $sell_task=new Task();
                        $sell_task->campaign_id = $this->id;
                        $sell_task->account_id = $account;
                        $sell_task->promotion_id=0;
                        $sell_task->status=0;
                        $sell_task->sell=1;
                        $sell_task->currency_one=$local_symbol;
                        $sell_task->currency_two=$this->entrance_currency;
                        $sell_task->start_rate=$trading_pair->statistics[0]->bid;//тут коеф профита
                        $sell_task->rate=$trading_pair->statistics[0]->bid*($this->strategy['profit']>1?$this->strategy['profit']:$this->strategy['profit']+1);//тут коеф профита
                        $tokens_count=0.01;
                        foreach ($this->balances[$account]->balances as $balance){
                            if($balance->name==$local_symbol){
                                $tokens_count=$balance->value;
                            }
                        }


//                        ApiRequest::statistics('v1/forecast/create',[
//                            'currency_one'=>$currency_one->id,
//                            'currency_two'=>$currency_two->id,
//                            'entry1'=>$trading_pair->statistics[0]->bid*0.995,
//                            'entry2'=>$trading_pair->statistics[0]->bid*1.001,
//                            'exit1'=>$sell_task->rate*0.995,
//                            'exit2'=>$sell_task->rate*1.001,
//                            'stop'=>$trading_pair->statistics[0]->bid*(1-$this->strategy['stop_loss']),
//                            'timeframe'=>$this->timeout
//                        ]);
                        if($tokens_count<0.000001){
                            echo 'skip';
                            continue;
                        }

                        $sell_task->tokens_count=$tokens_count*0.99;
                        //если ставка меньше  $1.5, то не ставить ордер
                        if($sell_task->tokens_count*$sell_task->rate*$this->entrance_usdt[0]->bid<1.5){
                            echo 'skep2 usdt';
                            continue;
                        }
                        //если тотал ордера меньше доллара то не допускать
                        if(isset($usdt_rates[$currency_one->id]) && $usdt_rates[$currency_one->id]->rate*$sell_task->tokens_count<1){
                            echo 'tokens count' . $usdt_rates[$currency_one->id]->rate."  ".($usdt_rates[$currency_one->id]->rate*$sell_task->tokens_count);
                            continue;
                        }

                        echo 'usdt-->'.number_format($sell_task->tokens_count*$sell_task->rate*$this->entrance_usdt[0]->bid,2);
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
                            echo "  skip3  ";
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
    public function check(){
        $tasks=Task::find()->where(['promotion_id'=>0])
            ->andWhere(['in','status',[2]])->andWhere(['>','time',time()-$this->timeout])
            ->andWhere(['in','account_id',$this->accounts])
            ->all();
        foreach ($tasks as $task){
            $order_trading_pair=$task->currency_one.$task->currency_two;
            foreach ($this->trading_pairs as $trading_pair){
                if($trading_pair->trading_paid==$order_trading_pair){//найдем нашу пару
                    //если цена сильно просела отменим
                    if($task->start_rate!=0 && 1-($trading_pair->statistics[0]->bid/$task->start_rate)>$this->strategy['stop_loss']){
                    //if(true){
                        //отменяем ордер
                       $task->cancelOrder();
                       Log::log(['cancel because of danger '.$task->id." ".$task->time]);
                        //$task->status=6; //новый статус отменено потому что опасно
                        //$task->save();
                        //если это был ордер на продажу выйти изэтой позиции
                        if($task->sell==1){
                            $sell_task=new Task();
                            $sell_task->campaign_id = $this->id;
                            $sell_task->account_id = $task->account_id;
                            $sell_task->promotion_id=0;
                            $sell_task->status=0;
                            $sell_task->sell=1;
                            $sell_task->currency_one=$task->currency_one;
                            $sell_task->currency_two=$task->currency_two;

                            $sell_task->start_rate=$trading_pair->statistics[0]->bid;
                            $sell_task->rate=$trading_pair->statistics[0]->bid*0.9;///что-б точно закрыть

                            $sell_task->tokens_count=$task->tokens_count;
                            $sell_task->random_curve=0;
                            $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                            $sell_task->progress=0;
                            $sell_task->time=time();

                            $sell_task->save();

                            $sell_task->make2(
                                Currency::find()->where(['symbol'=>$sell_task->currency_one])->limit(1)->one()->id,
                                Currency::find()->where(['symbol'=>$sell_task->currency_two])->limit(1)->one()->id);
                        }

                    }
                    //отмена из-за спада

                    echo "recent price-->".self::findThreshold($this->strategy['on_going_drop'],$trading_pair->statistics);
                    if($task->start_rate*($this->strategy['safe_area']>1?$this->strategy['safe_area']:$this->strategy['safe_area']+1)>$trading_pair->statistics[0]->bid
                        && self::findThreshold($this->strategy['on_going_drop'],$trading_pair->statistics)){
                        //if(true){
                        //отменяем ордер
                        $task->cancelOrder();
                        Log::log(['cancel because of drop '.$task->id." ".$task->time." "]);
                        //$task->status=6; //новый статус отменено потому что опасно
                        //$task->save();
                        //если это был ордер на продажу выйти изэтой позиции
                        if($task->sell==1){
                            $sell_task=new Task();
                            $sell_task->campaign_id = $this->id;
                            $sell_task->account_id = $task->account_id;
                            $sell_task->promotion_id=0;
                            $sell_task->status=0;
                            $sell_task->sell=1;
                            $sell_task->currency_one=$task->currency_one;
                            $sell_task->currency_two=$task->currency_two;

                            $sell_task->start_rate=$trading_pair->statistics[0]->bid;
                            $sell_task->rate=$trading_pair->statistics[0]->ask*0.9;// чтоб точно продать

                            $sell_task->tokens_count=$task->tokens_count;
                            $sell_task->random_curve=0;
                            $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                            $sell_task->progress=0;
                            $sell_task->time=time();

                            $sell_task->save();

                            $sell_task->make2(
                                Currency::find()->where(['symbol'=>$sell_task->currency_one])->limit(1)->one()->id,
                                Currency::find()->where(['symbol'=>$sell_task->currency_two])->limit(1)->one()->id);
                        }

                    }
                }
            }
        }

    }


    public function closeOutdated(){//добавить id  кампании
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;

        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'includes'=>'BTC']);
        $trading_pairs=$trading_pairs->data;

        foreach ($trading_pairs as $trading_pair){
            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
            $trading_pair->statistics=$tmp->data;
        }

        $this->trading_pairs=$trading_pairs;
        $this->entrance_usdt=$entrance_usdt;


        $tasks=Task::find()->where(['promotion_id'=>0])
        ->andWhere(['not in','status',[0,1,4,5]])->andWhere(['<','time',time()-$this->timeout])
        ->andWhere(['>','time',1])->andWhere(['in','account_id',$this->accounts])
//        ->createCommand()->rawSql;
//        echo $tasks; die();
            ->all();

        foreach ($tasks as $task){


            $task->cancelOrder();
            echo " ".$task->id;
            Log::log(['Timeout cancel ID '.$task->id." ".$task->time]);

            $order_trading_pair=$task->currency_one.$task->currency_two;
            foreach ($this->trading_pairs as $trading_pair) {
                if ($trading_pair->trading_paid == $order_trading_pair) {//найдем нашу пару
                    //если это был ордер на продажу выйти изэтой позиции
                    if($task->sell==1){
                        $sell_task=new Task();
                        $sell_task->campaign_id = $this->id;
                        $sell_task->account_id = $task->account_id;
                        $sell_task->promotion_id=0;
                        $sell_task->status=0;
                        $sell_task->sell=1;
                        $sell_task->currency_one=$task->currency_one;
                        $sell_task->currency_two=$task->currency_two;

                        $sell_task->start_rate=$trading_pair->statistics[0]->bid;
                        $sell_task->rate=$trading_pair->statistics[0]->ask*0.9;

                        $sell_task->tokens_count=$task->tokens_count;
                        $sell_task->random_curve=0;
                        $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                        $sell_task->progress=0;
                        $sell_task->time=time();

                        $sell_task->save();

                        $sell_task->make2(
                            Currency::find()->where(['symbol'=>$sell_task->currency_one])->limit(1)->one()->id,
                            Currency::find()->where(['symbol'=>$sell_task->currency_two])->limit(1)->one()->id);
                    }
                }
            }
        }

        $this->getBalance();
    }
    public function index($trading_pairs){
//        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['rating'=>1,'includes'=>$this->entrance_currency]);
//        $trading_pairs=$trading_pairs->data;
//
//        foreach ($trading_pairs as $trading_pair){
//            $tmp=ApiRequest::statistics('v1/trader2/info',['pair'=>$trading_pair->trading_paid]);
//            $trading_pair->statistics=$tmp->data;
//
//        }
//        //полумать что будет если USDTUSDT
//        //получим входную  валюту
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $entrance_usdt=$entrance_usdt->data;

        $this->trading_pairs=$trading_pairs;
        $this->entrance_usdt=$entrance_usdt;
        //return;
        $this->check();
        //return;
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
    public function getBalanceDate($timestamp){
        $balances=[];
        $accounts=$this->accounts;
        $this->current_usdt_volume=0;
        foreach ($accounts as $account){
            $tmp= ApiRequest::statistics('v1/account/get-balance-time', ['id'=>$account,'timestamp'=>$timestamp]);
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
            if(isset($_POST['accounts']) && !in_array($account_id,$_POST['accounts']))
                continue;
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
                    $buy_task->campaign_id = $this->id;
                    $buy_task->account_id = $account_id;
                    $buy_task->promotion_id=0;
                    $buy_task->status=0;
                    $buy_task->sell=0;
                    $buy_task->rate=$this->entrance_usdt[0]->bid*1.005;//чтоб точно купить
                    $buy_task->tokens_count=$balance->value/$buy_task->rate*0.998;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                    $buy_task->random_curve=0;
                    $buy_task->value=($buy_task->tokens_count*$buy_task->rate);
                    $buy_task->progress=0;
                    $buy_task->time=time();
                    $buy_task->is_user=1;
                    $buy_task->currency_one=$currency_one->symbol;
                    $buy_task->currency_two=$currency_two->symbol;
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
            if(isset($_POST['accounts']) && !in_array($account_id,$_POST['accounts']))
                continue;
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
                    $sell_task->campaign_id = $this->id;
                    $sell_task->account_id = $account_id;
                    $sell_task->promotion_id=0;
                    $sell_task->status=0;
                    $sell_task->sell=1;
                    $sell_task->rate=$trading_pair->statistics[0]->ask*1.05;//чтоб точно купить
                    $sell_task->tokens_count=$balance->value;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                    $sell_task->random_curve=0;
                    $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                    $sell_task->progress=0;
                    $sell_task->time=time();
                    $sell_task->is_user=1;
                    $sell_task->currency_one=$currency_one->symbol;
                    $sell_task->currency_two=$currency_two->symbol;

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
        $trading_pairs=ApiRequest::statistics('v1/trader2/list',['includes'=>'USDT','limit'=>999]);

        $trading_pairs=$trading_pairs->data;
        $currency_to_usdt=ApiRequest::statistics('v1/currency/usdt-rates');
        $usdt_rates=[];
        foreach ($currency_to_usdt->data->rates as $usdt_rate){
            $usdt_rates[$usdt_rate->currency]=$usdt_rate;
        }

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
            if(isset($_POST['accounts']) && !in_array($account_id,$_POST['accounts']))
                continue;
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
                $sell_task->campaign_id = $this->id;
                $sell_task->account_id = $account_id;
                $sell_task->promotion_id=0;
                $sell_task->status=0;
                $sell_task->sell=1;
                $sell_task->rate=$trading_pair->statistics[0]->ask*0.9;//чтоб точно купить
                $sell_task->tokens_count=$balance->value;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                $sell_task->random_curve=0;
                $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                $sell_task->progress=0;
                $sell_task->time=time();
                $sell_task->is_user=1;
                $sell_task->currency_one=$currency_one->symbol;
                $sell_task->currency_two=$currency_two->symbol;
                //если тотал ордера меньше доллара то не допускать
                if(isset($usdt_rates[$currency_one->id]) && $usdt_rates[$currency_one->id]->rate*$sell_task->tokens_count<1){
                    echo "(".$currency_one->symbol.$currency_two->symbol." ".$sell_task->tokens_count." ".($usdt_rates[$currency_one->id]->rate*$sell_task->tokens_count).")";
                    continue;
                }
                else{
                    echo "(".$currency_one->symbol.$currency_two->symbol." ".($usdt_rates[$currency_one->id]->rate*$sell_task->tokens_count).")";
                }
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
    public  function sellTask(Currency $currency_one, Currency $currency_two,$trading_pair, $is_buy=false,$percent_bank,$take_profit=false,$is_user=0){
        $entrance_usdt=ApiRequest::statistics('v1/trader2/info',['pair'=>$this->entrance_currency.'USDT']);
        $this->entrance_usdt=$entrance_usdt=$entrance_usdt->data;
        if(empty($this->balances))
            $balances=$this->getBalance();

        if(!$take_profit)
            $take_profit=$this->strategy['profit']>1?$this->strategy['profit']:$this->strategy['profit']+1;
        //print_r($this->balances);
        if($is_buy){
            foreach ($this->balances as $account_id=>$balances) {

                foreach ($balances->balances as $balance) {

                    if($balance->name!=$currency_one->symbol)
                        continue;

                    $tokens_we_need=$balances->in_usd*$percent_bank/
                        $trading_pair[0]->bid/$this->entrance_usdt[0]->bid;
                    print_r($tokens_we_need);
                    echo  "<br>";
                    //continue;
                    if($is_buy &&  $balance->value<$tokens_we_need){
                        $buy_task=new Task();
                        $buy_task->campaign_id = $this->id;
                        $buy_task->account_id = $account_id;
                        $buy_task->promotion_id=0;
                        $buy_task->status=0;
                        $buy_task->sell=0;
                        $buy_task->currency_one=$currency_one->symbol;
                        $buy_task->currency_two=$currency_two->symbol;
                        $buy_task->rate=$trading_pair[0]->bid*1.05;//чтоб точно купить
                        $buy_task->start_rate=$trading_pair[0]->bid;//чтоб точно купить
                        $buy_task->tokens_count=$tokens_we_need-$balance->value;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                        $buy_task->random_curve=0;
                        $buy_task->value=($buy_task->tokens_count*$buy_task->rate);
                        $buy_task->progress=0;
                        $buy_task->time=time();
                        if($buy_task->tokens_count*$buy_task->rate*$this->entrance_usdt[0]->bid<1.5)
                            continue;
                        $buy_task->save();
                        //print_r($buy_task->errors);
                        $buy_task->make2($currency_one->id,$currency_two->id);
                        $balance->value+=$buy_task->tokens_count;
                        //sleep(1);
                    }

                }
            }
        }
        echo '--------------';
        //$balances=$this->getBalance();

        foreach ($this->balances as $account_id=>$balances) {

            foreach ($balances->balances as $balance) {
                if($balance->name!=$currency_two->symbol)
                    continue;
                $tokens_we_need=$balances->in_usd*$percent_bank/
                    $trading_pair[0]->ask/$this->entrance_usdt[0]->ask;
                print_r($tokens_we_need);
                echo  "<br>";
                //continue;

                $sell_task=new Task();
                $sell_task->campaign_id = $this->id;
                $sell_task->account_id = $account_id;
                $sell_task->promotion_id=0;
                $sell_task->status=0;
                $sell_task->sell=1;
                $sell_task->start_rate=$trading_pair[0]->bid;//чтоб точно купить
                $sell_task->rate=$trading_pair[0]->ask*(1+$take_profit);//чтоб точно купить
                $sell_task->tokens_count=$tokens_we_need*0.99;//посчитал по цене покупки чтоб не пролететь по минималкам и комиссиям
                $sell_task->random_curve=0;
                $sell_task->value=($sell_task->tokens_count*$sell_task->rate);
                $sell_task->progress=0;
                $sell_task->time=time();
                $sell_task->currency_one=$currency_one->symbol;
                $sell_task->currency_two=$currency_two->symbol;
                $sell_task->time=time();

                if($sell_task->tokens_count<0.000000001){
                    $market=Market::findOne(Account::findOne($account_id)->type);
                    echo "---------------$market->name  ".$currency_one->symbol." ".$currency_two->symbol." $sell_task->tokens_count low amt ------------";
                    continue;
                }

                $sell_task->save();
                print_r($sell_task->errors);
                $sell_task->make2($currency_one->id,$currency_two->id);
            }
        }
    }

    public function getTriggerScore($symbol){
        $trigger_score=0;
        foreach ($this->settings as $key=>$strategy){
            //тут мой код но его нужно потом завернуть в if  и переделать
            foreach ($this->trading_pairs as $tp){
                if($tp->trading_paid==$symbol.$this->entrance_currency){
                    $trading_pair=$tp;
                    break;
                }
            }
            if($strategy['parameter']=='bounce_rate'){
                if(self::findThresholdBreak($strategy['percent_drop'],$strategy['percent_bounce'],$trading_pair->statistics))
                    $trigger_score+=$strategy['value'];
            }
            if($strategy['parameter']=='depth'){
                $chance=DepthAnalizer::getPossibility($trading_pair->trading_paid);
                switch ($strategy['sign']){
                    case '>':
                        if($chance>$strategy['limit'])
                            $trigger_score+=$strategy['value'];
                        break;
                    case '<':
                        if($chance<$strategy['limit'])
                            $trigger_score+=$strategy['value'];
                        break;
                    default:
                        if($chance>$strategy['limit'])
                            $trigger_score+=$strategy['value'];
                        break;
                }
            }
        }




        //return 100;

        return $trigger_score;
    }

    public static function findThresholdBreak($percent_drop,$percent_bounce,$statistics){

        $asks=array_column($statistics,'bid');
        $now=$asks[0];
        $max=max($asks);
        $min=min($asks);

        if(($max-$min)/$min>$percent_drop && ($now-$min)/$min>$percent_bounce )
            return 100;


        return 0;

    }

    //вернет TRUE   если курс изменился в низ на заданный процент, иначе FALSE
    public static function findThreshold($percent_drop,$statistics){
        $asks=array_column($statistics,'bid');
        $now=$asks[0];
        $max=max($asks);
        $min=min($asks);
        if(1-($now/$min)>$percent_drop)
            return true;

        return false;

    }
    private function weNeedToPlaceBuyOrder($account,$random_per,$local_symbol,$exchange_price){
        //проверяем есть ли у нас столько монеты
        $we_have=0;
        $this->balances[$account]->balances;
        foreach ($this->balances[$account]->balances as $balance){
            if($balance->name==$local_symbol){
                if($balance->value*$exchange_price*$this->entrance_usdt[0]->bid>=$random_per*$this->balances[$account]->in_usd)
                    $we_have=1;
            }
        }
        return !$we_have;
    }
}
