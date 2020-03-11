<?php
/* @var $this yii\web\View */
/* @var $possibility array */
/* @var $companies array */
/* @var $trading_pairs array */
/* @var $balances array */

use common\models\Task;
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
    <li class="active"><a data-toggle="tab" class="except" href="#orders">Orders</a></li>
    <li><a data-toggle="tab" class="except" href="#control">control</a></li>
    <li><a data-toggle="tab" class="except" href="#statistics">statistics</a></li>

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
                            <td>
                                <?php
                                $color="white";
                                $icon="";
                                ?>
                                <?php if($t->status!=\common\models\Task::STATUS_CANCELED  && $t->start_rate){
                                    if($t->start_rate){
                                        if($t->sell){
                                            if($t->start_rate>$t->rate) {
                                                $color="red";
                                                $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                                            }else{
                                                $color="lime";
                                                $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                                            }
                                        }else{
                                            if($t->start_rate>$t->rate) {
                                                $color="lime";
                                                $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                                            }else{
                                                $color="red";
                                                $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                                            }
                                        }

                                    }

                                }elseif(!$t->start_rate && $t->status!=\common\models\Task::STATUS_CANCELED){


                                   if(isset($trading_pairs[$t->currency_one.$t->currency_two])){
                                       $time=$t->time*1000;
                                       foreach ($trading_pairs[$t->currency_one.$t->currency_two]->statistics as $time_milliseconds=>$stat ){
                                           if(abs($time-$time_milliseconds)>350*1000 &&  abs($time-$time_milliseconds)<900*1000){
                                               if($t->sell){
                                                   if($stat->open>$t->rate) {
                                                       $color="red";
                                                       $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                                                   }else{
                                                       $color="lime";
                                                       $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                                                   }
                                               }else{
                                                   if($stat->open>$t->rate) {
                                                       $color="lime";
                                                       $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                                                   }else{
                                                       $color="red";
                                                       $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                                                   }
                                               }
                                               break;
                                           }
                                       }
                                   }
                                }

                                ?>
                                <span class="indicator" style="color: <?=$color?>">
                                    <?= number_format($t->rate,4);?>
                                    <?= $icon?>
                                </span>


                            </td>
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
        <?php
        $pairs=[];
        foreach ($orders  as $order){
            $do_break=0;
            foreach ($pairs as $pair){
                if($pair['currency_one']==$order->currency_one &&
                    $pair['currency_two']==$order->currency_two){
                    $do_break=1;//такая валюта уже ест
                    break;
                }
            }
            if($do_break)
                continue;
            $p=[];
            $p['currency_one']=$order->currency_one;
            $p['currency_two']=$order->currency_two;
            $pairs[]=$p;
        }
        ?>
        <h3>Statistics</h3>
        <?php foreach($pairs as $pair){ ?>
            <button class="btn btn-default statistics-button" onclick="loadPairStatistics('<?= $pair['currency_one']?>','<?=$pair['currency_two'] ?>')"><?php echo $pair['currency_one']. "/" .$pair['currency_two'] ?></button>
        <?php } ?>
        <div class="statistics-wrapper">

        </div>
        <div class="orders-wrapper">

        </div>
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
    var trading_pairs=<?= json_encode($trading_pairs);?>
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
    function createOrder() {
        var data={
            'campaign_id':<?= $campaign->id?>,
            'currency_one':$('#symbol1').val(),
            'currency_two':$('#symbol2').val(),
            'percent':$('#percent_amt').val(),
            'profit':$('#percent_profit').val(),
            'is_buy':$('#is_buy').prop('checked'),
        };
        $.ajax({
            type : 'POST',
            url : '/trader2/manual-order',
            data : data
        }).done(function(data) {
            console.log(data)
        }).fail(function() {
            // Если произошла ошибка при отправке запроса
            $("#output").text("error3");
        })
        console.log(data)
    }

    function loadPairStatistics(currency_one,currency_two) {
        $('.statistics-button').removeClass('btn-primary').addClass('.btn-default');
        $(this.event.target).addClass('btn-primary');
        console.log(this.event.target)
        $.ajax({
            type : 'POST',
            url : '/trader2/load-pair-statistics',
            data : {
                currency_one:currency_one,
                currency_two:currency_two,
                id:<?= $campaign->id?>
            }
        }).done(function(data) {
            buildTable(data)
            BuildStatTable(data)
        }).fail(function() {
            // Если произошла ошибка при отправке запроса
            $("#output").text("error3");
        })
    }
    function BuildStatTable(data) {
        var order_statistics={
            day:{
                total:0,
                failed:0,
                open:0,
                completed:0,
                canceled:0,
                buy:0,
                sell:0,
                profit:0,
                profitable_orders:0,
                usdt_dif:0,
            },
            hour:{
                total:0,
                failed:0,
                open:0,
                completed:0,
                canceled:0,
                buy:0,
                sell:0,
                profit:0,
                profitable_orders:0,
                usdt_dif:0,
            },
            week:{
                total:0,
                failed:0,
                open:0,
                completed:0,
                canceled:0,
                buy:0,
                sell:0,
                profit:0,
                profitable_orders:0,
                usdt_dif:0,
            }
        }
        var time=new Date().getTime() /1000;
        for (var i=0;i<data.length;i++){
            var order=data[i];
            if(order.time>time-3600){
                order_statistics['hour']['total']+=1;
                if(order.status==<?= Task::STATUS_STARTED?> ) order_statistics['hour']['failed']++;
                if(order.status==<?= Task::STATUS_CREATED?>) order_statistics['hour']['open']++;
                if(order.status==<?= Task::STATUS_COMPLETED?>) order_statistics['hour']['completed']++;
                if(order.status==<?=Task::STATUS_CANCELED?>) order_statistics['hour']['canceled']++;
                if(order.sell) order_statistics['hour']['sell']++;
                else order_statistics['hour']['buy']++;
            }
            if(order.time>time-24*3600){
                order_statistics['day']['total']+=1;
                if(order.status==<?= Task::STATUS_STARTED?> ) order_statistics['day']['failed']++;
                if(order.status==<?= Task::STATUS_CREATED?>) order_statistics['day']['open']++;
                if(order.status==<?= Task::STATUS_COMPLETED?>) order_statistics['day']['completed']++;
                if(order.status==<?=Task::STATUS_CANCELED?>) order_statistics['day']['canceled']++;
                if(order.sell) order_statistics['day']['sell']++;
                else order_statistics['day']['buy']++;
            }
            if(order.time>time-7*24*3600){
                order_statistics['week']['total']+=1;
                if(order.status==<?= Task::STATUS_STARTED?> ) order_statistics['week']['failed']++;
                if(order.status==<?= Task::STATUS_CREATED?>) order_statistics['week']['open']++;
                if(order.status==<?= Task::STATUS_COMPLETED?>) order_statistics['week']['completed']++;
                if(order.status==<?=Task::STATUS_CANCELED?>) order_statistics['week']['canceled']++;
                if(order.sell) order_statistics['week']['sell']++;
                else order_statistics['week']['buy']++;
            }
        }
        for (var i=0;i<data.length;i++){
            var order=data[i];
            if(order.status==<?= Task::STATUS_COMPLETED?>) {
                var profit = 0;
                var dif = 0;
                var was = 0;
                var became = 0;
                if (order.start_rate) {
                    if (order.sell)
                        profit = order.rate / order.start_rate * 100 - 100;
                    else
                        profit = order.start_rate / order.rate * 100 - 100;

                    //определяем размер  ставки в usdt
                    if (trading_pairs[order['currency_two'] + 'USDT'] != undefined) {
                        for (const [time_milliseconds, stat] of Object.entries(trading_pairs[order['currency_two'] + 'USDT'].statistics)) {
                            if (Math.abs(time*1000 - parseInt(time_milliseconds)) > 350 * 1000 && Math.abs(time*1000 - parseInt(time_milliseconds)) < 900 * 1000) {
                                was=order.tokens_count*order.start_rate*stat.open;
                                became=order.tokens_count*order.rate*stat.open;
                                break;
                            }
                        }
                    }
                    if (order.time > time - 3600) {
                        order_statistics['hour']['profit'] += profit;
                        order_statistics['hour']['profitable_orders'] += profit > 0 ? 1 : 0;
                        order_statistics['hour']['usdt_dif'] += was-became;
                    }
                    if (order.time > time - 24 * 3600) {
                        order_statistics['day']['profit'] += profit;
                        order_statistics['day']['profitable_orders'] += profit > 0 ? 1 : 0;
                        order_statistics['day']['usdt_dif'] += was-became;

                    }
                    if (order.time > time - 7 * 24 * 3600) {
                        order_statistics['week']['profit'] += profit;
                        order_statistics['week']['profitable_orders'] += profit > 0 ? 1 : 0;
                        order_statistics['week']['usdt_dif'] += was-became;
                    }
                }else {
                    if (trading_pairs[order['currency_one'] + order['currency_two']] != undefined) {
                        for (const [time_milliseconds, stat] of Object.entries(trading_pairs[order['currency_one'] + order['currency_two']].statistics)) {
                            if (Math.abs(time*1000 - parseInt(time_milliseconds)) > 350 * 1000 && Math.abs(time*1000 - parseInt(time_milliseconds)) < 900 * 1000) {
                                if (order.sell){
                                    profit = order.rate / stat['open'] * 100 - 100;
                                }
                                else{
                                    profit = stat['open'] / order.rate * 100 - 100;
                                }
                                //определяем размер  ставки в usdt
                                was=order.tokens_count*trading_pairs[order['currency_one'] + 'USDT'].statistics[time_milliseconds].open;
                                became=order.tokens_count*order.rate*trading_pairs[order['currency_two'] + 'USDT'].statistics[time_milliseconds].open;

                                if (order.time > time - 3600) {
                                    order_statistics['hour']['profit'] += profit;
                                    order_statistics['hour']['profitable_orders'] += profit > 0 ? 1 : 0;
                                    order_statistics['hour']['usdt_dif'] += was-became;
                                }
                                if (order.time > time - 24 * 3600) {
                                    order_statistics['day']['profit'] += profit;
                                    order_statistics['day']['profitable_orders'] += profit > 0 ? 1 : 0;
                                    order_statistics['day']['usdt_dif'] += was-became;
                                }
                                if (order.time > time - 7 * 24 * 3600) {
                                    order_statistics['week']['profit'] += profit;
                                    order_statistics['week']['profitable_orders'] += profit > 0 ? 1 : 0;
                                    order_statistics['week']['usdt_dif'] += was-became;
                                }

                                break;
                            }

                        }

                    }
                }
            }
        }
        var html="<table style=\"    width: 100%;\n" +
            "    margin-bottom: 17px;\n" +
            "    border-radius: 3px;\">\n" +
            "            <tr>\n" +
            "                <td>\n" +
            "                    <span style=\"color: red\">"+order_statistics['week']['failed']+"</span>/\n" +
            "                    <span style=\"color: orange\">"+order_statistics['week']['canceled']+"</span>/\n" +
            "                    <span style=\"color: lightskyblue\">"+order_statistics['week']['completed']+"</span>/\n" +
            "                    <span style=\"color: lightgreen\">"+order_statistics['week']['open']+"</span>/\n" +
            "                    <span style=\"color: white\">"+order_statistics['week']['total']+"</span>\n" +
            "                </td>\n" +
            "                <td>\n" +
            "                    <span style=\"color: red\">"+order_statistics['day']['failed']+"</span>/\n" +
            "                    <span style=\"color: orange\">"+order_statistics['day']['canceled']+"</span>/\n" +
            "                    <span style=\"color: lightskyblue\">"+order_statistics['day']['completed']+"</span>/\n" +
            "                    <span style=\"color: lightgreen\">"+order_statistics['day']['open']+"</span>/\n" +
            "                    <span style=\"color: white\">"+order_statistics['day']['total']+"</span>\n" +
            "                </td>\n" +
            "                <td>\n" +
            "                    <span style=\"color: red\">"+order_statistics['hour']['failed']+"</span>/\n" +
            "                    <span style=\"color: orange\">"+order_statistics['hour']['canceled']+"</span>/\n" +
            "                    <span style=\"color: lightskyblue\">"+order_statistics['hour']['completed']+"</span>/\n" +
            "                    <span style=\"color: lightgreen\">"+order_statistics['hour']['open']+"</span>/\n" +
            "                    <span style=\"color: white\">"+order_statistics['hour']['total']+"</span>\n" +
            "                </td>\n" +
            "            </tr>\n" +
            "            <tr>\n" +
            "                <td>week</td>\n" +
            "                <td>day</td>\n" +
            "                <td>hour</td>\n" +
            "            </tr>\n" +
            "            <tr>\n" +
            "                <td>\n" + order_statistics['week']['profitable_orders']+"/"+order_statistics['week']['total']+" ("+ (order_statistics['week']['profit']/order_statistics['week']['total']).toFixed(2)+"%)</td> \n"+
            "                <td>\n" + order_statistics['day']['profitable_orders']+"/"+order_statistics['day']['total']+" ("+ (order_statistics['day']['profit']/order_statistics['day']['total']).toFixed(2)+"%)</td> \n"+
            "                <td>\n" + order_statistics['hour']['profitable_orders']+"/"+order_statistics['hour']['total']+" ("+ (order_statistics['hour']['profit']/order_statistics['hour']['total']).toFixed(2)+"%)</td> \n"+

"            </tr>\n"+
            "            <tr>\n" +
            "                <td>\n" + order_statistics['week']['usdt_dif'].toFixed(2)+"</td> \n"+
            "                <td>\n" + order_statistics['day']['usdt_dif'].toFixed(2)+"</td> \n"+
            "                <td>\n" + order_statistics['hour']['usdt_dif'].toFixed(2)+"</td> \n"+

            "            </tr>\n"+
"        </table>";
        $('.statistics-wrapper').html(html);
        console.log(order_statistics)
    }



    function buildTable(data) {
        var html="<table>";
        html+="<thead>\n" +
            "                    <tr>\n" +
            "                        <th>id</th>\n" +
            "                        <th>direction</th>\n" +
            "                        <th>acc</th>\n" +
            "                        <th>date</th>\n" +
            "                        <th>Currency one\n" +
            "                        <th>Currency two</th>\n" +
            "                        <th>tokens_count</th>\n" +
            "                        <th>rate start</th>\n" +
            "                        <th>rate</th>\n" +
            "                        <th>status</th>\n" +
            "                        <th>action</th>\n" +
            "                    </tr>\n" +
            "                    </thead>";

        html+="<tbody>";
        for (var i=0;i<data.length;i++){
            html+=buildRow(data[i]);
        }
        html+="</tbody>";
        html+="</table>";
        $('.statistics-chart').html(html)
    }
    function buildRow(order){
        var html="<tr>";

        html+="<td>"+order['id']+"</td>"
        html+="<td>"+(order['sell']==1?'<b style="color:orange">sell</b>':'<b style="color:purple;">buy</b>')+"</td>"
        html+="<td>"+order['account_id']+"</td>"
        html+="<td>"+dateFormat("d/m/y H:i",new Date(order['time']*1000))+"</td>"
        html+="<td>"+order['currency_one']+"</td>"
        html+="<td>"+order['currency_two']+"</td>"
        html+="<td>"+order['tokens_count']+"</td>"
        html+="<td>"+order['start_rate']+"</td>"
        html+="<td>"+buildInfo(order)+"</td>"
        html+="<td>"+buildStatus(order)+"</td>"
        html+="<td>"+"</td>"

        html+="</tr>";
        return html;
    }
    function buildInfo(order) {
        var color='white';
        var icon="";
        if(order['status']!=<?= \common\models\Task::STATUS_CANCELED?> && order['start_rate']){
            if(order['sell']){
                if(order['start_rate']>order['rate']){
                    color='red';
                    icon='<i class="fa fa-caret-down" style=\'color:'+color+'\'></i>';
                }else{
                    color='lime';
                    icon='<i class="fa fa-caret-up" style=\'color:'+color+'\'></i>';
                }
            }else{
                if(order['start_rate']>order['rate']){
                    color='lime';
                    icon='<i class="fa fa-caret-down" style=\'color:'+color+'\'></i>';
                }else{
                    color='red';
                    icon='<i class="fa fa-caret-up" style=\'color:'+color+'\'></i>';
                }
            }



        }else if(order['status']!=<?= \common\models\Task::STATUS_CANCELED?> ){
            var time=new Date().getTime() ;
            if(trading_pairs[order['currency_one']+order['currency_two']]!=undefined){
                for (const [time_milliseconds, stat] of Object.entries(  trading_pairs[order['currency_one']+order['currency_two']].statistics)) {
                    if(Math.abs(time-parseInt(time_milliseconds))>350*1000 && Math.abs(time-parseInt(time_milliseconds))<900*1000){

                        if(order['sell']){
                            if(stat['open']>order['rate']){
                                color='red';
                                icon='<i class="fa fa-caret-down" style=\'color:'+color+'\'></i>';
                            }else{
                                color='lime';
                                icon='<i class="fa fa-caret-up" style=\'color:'+color+'\'></i>';
                            }
                        }else{
                            if(stat['open']>order['rate']){
                                color='lime';
                                icon='<i class="fa fa-caret-down" style=\'color:'+color+'\'></i>';
                            }else{
                                color='red';
                                icon='<i class="fa fa-caret-up" style=\'color:'+color+'\'></i>';
                            }
                        }
                        break;
                    }
                }

            }
            /*for(var j=0;j<trading_pairs[order['currency_one']+order['currency_two'].statistics;j++]){
                if(time-)
            }*/
        }

        return '<span style="color:'+color+'" >'+order['rate']+icon+'</span>';
    }
    function buildStatus(order) {
        if(order.status==1) return '<b style=\'color:red\'>error</b>';
        if(order.status==2) return 'OK <b style="color:red"> ('+order.progress+'%)</b>';
        if(order.status==3) return 'price error';
        if(order.status==4) return '<span class="canceled">canceled by system</span>';
        if(order.status==5) return '<span class="completed">completed</span>';
        return  order.status
    }
</script>