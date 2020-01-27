<?php
/* @var $this yii\web\View */
/* @var $possibility array */
/* @var $companies array */
/* @var $trading_pairs array */
/* @var $balances array */

use common\components\ApiRequest;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);

function getPeriod($date){
    $hours=date("H",$date);
    if($hours<6)
        return 1;
    if($hours<12)
        return 2;
    if($hours<18)
        return 3;
    if($hours<25)
        return 4;
}
?>




<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>


<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#orders">Orders</a></li>
    <li><a data-toggle="tab" href="#control">control</a></li>
    <li><a data-toggle="tab" href="#statistics">statistics</a></li>

</ul>

<div class="tab-content">

    <div id="orders" class="tab-pane fade in active">
        <div class="row">
            <div class="col-md-11">
                <h3>History:</h3>
                <table>
                    <thead>
                    <tr>
                        <th>id</th>
                        <th>direction</th>
                        <th>acc</th>
                        <th>date</th>
                        <th>Currency one
                        <th>Currency two</th>
                        <th>tokens_count</th>
                        <th>rate start</th>
                        <th>rate</th>
                        <th>status</th>
                        <th>action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <? foreach($orders as $t): ?>
                        <tr>
                            <td><?=$t->id;?></td>
                            <td><?=($t->sell==1)?'<b style="color:orange">sell</b>':'<b style="color:purple;">buy</b>';?></td>
                            <td><?=$t->account_id;?></td>
                            <td><?=date("d/m/y H:i", $t->time);?></td>
                            <td><?=$t->currency_one;?></td>
                            <td><?=$t->currency_two;?></td>
                            <td><?=$t->tokens_count;?></td>
                            <td><?=$t->start_rate;?></td>
                            <td><?=$t->rate;?></td>
                            <td><? if($t->status==1){echo "<b style='color:red'>error</b>";} else if($t->status==2){echo "OK";
                                    if($t->progress != 100) {
                                        echo '<b style="color:red"> ('.$t->progress.'%)</b>';
                                    }
                                }else if($t->status==3){
                                    echo "price error";
                                } else {
                                    echo '<span class="'.$t::$statuses[$t->status].'">'.$t::$statuses[$t->status].'</span>';
                                }

                                ?></td>
                            <td>
                                <?php if($t->status==2){ ?>
                                    <a target="_blank" href="/promotion/cancel?id=<?=$t->id?>">cancel</a>
                                <?php } ?>
                            </td>

                        </tr>

                    <? endforeach; ?>
                    <tbody>
                </table>
                <pre>
        <?php
        $data1=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',strtotime($date_start)),'date_end'=>date('Y-m-d H:i:s',strtotime($date_start)+900),'limit'=>1]);
        reset($data1->data); //Ensure that we're at the first element
        $key = key($data1->data);
        $open1=$data1->data->{$key}->open;



        $data2=ApiRequest::statistics('v1/trader2/graphic',['symbol'=>'BTCUSDT','date_start'=>date('Y-m-d H:i:s',strtotime($date_start)+24*3600),'date_end'=>date('Y-m-d H:i:s',strtotime($date_start)+24*3600+900),'limit'=>1]);
        reset($data2->data); //Ensure that we're at the first element
        $key = key($data2->data);
        $open2=$data2->data->{$key}->open;



        ?>
            </pre>
                <h1>Прифит на торгах: <span style="color: lightgreen"><?= number_format($total_profit/10,2);   ?>%</span>; Колебание битка: <?= number_format(($open2-$open1)/$open1*100,2) ;?>% / $<?=$open2-$open1 ?></h1>
            </div>

        </div>
    </div>

    <div id="control" class="tab-pane fade">
        <h3>Create manual order</h3>
        <p>This will create sell Order on the markets</p>
        <div class="col-md-12">
            <div class="form-group">
                <label for="is_buy">Create buy order if NSF</label>
                <input  type="checkbox" class="form-control" id="is_buy" checked>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="symbol">Symbol</label>
                <input  class="form-control" id="symbol1" placeholder="ETH" value="ETH">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="symbol"> </label>
                <input  class="form-control" id="symbol2" placeholder="BTC" value="BTC">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="percent_amt">Percent Amount</label>
                <input  class="form-control" id="percent_amt" placeholder="10" value="10">
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="percent_profit">Take Profit %</label>
                <input  class="form-control" id="percent_profit" placeholder="5" value="5">
            </div>
        </div>

        <button class="btn btn-primry"  onclick="createOrder()" style="color: black" type="button">CreateOrder</button>
    </div>
    <div id="statistics" class="tab-pane fade">
        <h3>Statistics</h3>
        <div class="statistics-chart"></div>
    </div>
</div>




<style>

    table tr>* {
        padding: 5px;
        border-bottom: 1px solid #888;
    }
</style>
<style>
    table tr>* {
        border:1px solid #eee;
        padding:3px 10px;
        min-width:75px;
        text-align:center;
    }
    .listtopie-link {
        font-size:16px;
        margin: 3px 10px;
    }
    h4 {
        margin-bottom:20px;
        font-size:20px;
    }
    td.red {
        color:red;
    }
    td.green {
        color:#00d400;
    }
    .canceled{
        color: orange;
    }
    .completed{
        color: lime;
    }
</style>
<script>
    var statistics=<?= json_encode($statistics)?>;

</script>
<script>
    window.onload = function () {
        var data=[];
        for(var i=0;i<statistics['balances'].length;i++){
            var usd_total=0;
            $.each(statistics['balances'][i], function(account_id, balances) {
                usd_total+=(balances && ('in_usd' in balances))?balances.in_usd:0;
            });
            data.push({
                x:i,
                y:usd_total
            });
        }

            var options = {
                title: {
                    text: 'balances'
                },
                axisX:{
                    interval: 10,
                },
                axisY: {
                    // title: "If you need",
                    // suffix: "K",
                    includeZero: false
                },
                animationEnabled: true,
                exportEnabled: true,
                data: [
                    {
                        type: "spline", //change it to line, area, column, pie, etc
                        dataPoints: data,

                    }
                ]

            }
            $(".statistics-chart").CanvasJSChart(options);

    }



    function refresh() {

        //window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

</script>