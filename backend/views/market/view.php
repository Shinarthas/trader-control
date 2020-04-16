<?php

use common\components\ApiRequest;

?>
<h1><?= $market->name?> market Info</h1>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<div class="row">
    <div class="col-md-8">
        <h2>Accounts</h2>
        <div class="row">
            <?php $index=0; ?>
            <?php foreach ($accounts as $account){ ?>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading"><?= $account['name']?></div>
                        <div class="panel-body">
                            <div class="chart1" data-account="<?=$account['id']?>" data-index="<?=$index?>">
                                <canvas id="chart-1-<?=$account['id']?>" height="300px"></canvas>
                                <canvas id="chart-2-<?=$account['id']?>" height="280px"></canvas>
                            </div>

                        </div>

                        <div class="panel-footer">Total: <span class="total">$<?php echo number_format($account['balances'][count($account['balances'])-1]['total'],2) ?></span></div>
                    </div>
                </div>
            <?php $index++;?>
            <?php } ?>
        </div>
    </div>
</div>
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
<style>
    .panel-default > .panel-heading {
        color: #ffffff;
        background-color: #1f2121;
        border-color: #c3c3c3;
    }
    .panel-footer {

        background-color: #3d403f;

    }
    .panel {
        margin-bottom: 20px;
        background-color: #1e1f1f;
    }
    span.total{
        color: #4dd415;
        font-weight: 700;
        font-size: 20px;
    }
</style>
<script>
    var colorsC= {
        USDT: "#0eff1a",
        BTC: "#fff236",
        ETH: "#61655d",
        TRX: "#ff3847",
        BNB: "#da9aff",
        ZEC: "#ffc69e",
        XRP: "#5494ff",
        XMR: "#ff7a39",
    };

    var accounts=<?= json_encode($accounts)?>;
    window.onload = function () {
        $( ".chart1" ).each(function( index ) {
            var account_id=$(this).attr('data-account');
            var inder_index=$(this).attr('data-index');
            var canvas=$(this).find('canvas').eq(0)[0];
            var canvas2=$(this).find('canvas').eq(1)[0];
            var ctx = canvas2.getContext('2d');
            var balances=[];
            var  labels=[];

            for(var i=0;i<accounts[inder_index].balances.length;i++){

                if(accounts[inder_index].balances[i].total!=undefined){
                    balances.push(accounts[inder_index].balances[i].total)
                    labels.push(dateFormat('m/d H:i',new Date(accounts[inder_index].balances[i].date)))
                }

                else{
                    balances.push(0);
                    labels.push(dateFormat('m/d  H:i',""))
                }

            }
            var colors=[];
            for(var k=0;k<labels.length;k++){
                console.log(labels[k],hashCode(labels[k]),intToRGB(hashCode(labels[k])))
                if(colorsC[labels[k]]==undefined)
                    colors.push('#'+intToRGB(hashCode(labels[k]+'asd')))
                else
                    colors.push(colorsC[labels[k]])
            }
            console.log(balances);
            var data = {
                labels: labels,
                datasets: [
                    {
                        label: "Weekly Balance change history",
                        fill: false,
                        lineTension: 0.1,
                        backgroundColor: "rgba(75,192,192,0.4)",
                        borderColor: "rgba(75,192,192,1)",
                        borderCapStyle: 'butt',
                        borderDash: [],
                        borderDashOffset: 0.0,
                        borderJoinStyle: 'miter',
                        pointBorderColor: "rgba(75,192,192,1)",
                        pointBackgroundColor: "#fff",
                        pointBorderWidth: 1,
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(75,192,192,1)",
                        pointHoverBorderColor: "rgba(220,220,220,1)",
                        pointHoverBorderWidth: 2,
                        pointRadius: 5,
                        pointHitRadius: 10,
                        data: balances,
                    }
                ]
            };
            var option = {
                showLines: true
            };
            var myLineChart = Chart.Line(canvas,{
                data:data,
                options:option
            });

            var in_usd=[];
            var ms=[];
            var tmpData=accounts[inder_index].balances[accounts[inder_index].balances.length-1];
            console.log(tmpData);
            if(tmpData!=undefined){
                for(var i=0;i<tmpData.balances.length;i++){
                    if(tmpData.balances[i]==undefined)
                        continue;
                    if(typeof tmpData.balances[i].name !='undefined'){
                        if((parseFloat(tmpData.balances[i].value)
                            +parseFloat(tmpData.balances[i].value_in_orders))
                            *parseFloat(tmpData.balances[i].rate)>1){
                            ms.push(tmpData.balances[i].name);
                            in_usd.push((
                                parseFloat(tmpData.balances[i].value)
                                    +parseFloat(tmpData.balances[i].value_in_orders))
                                    *parseFloat(tmpData.balances[i].rate));
                        }

                    }
                }
                var labels=ms;
                var colors=[];
                for(var k=0;k<labels.length;k++){
                    console.log(labels[k],hashCode(labels[k]),intToRGB(hashCode(labels[k])))
                    if(colorsC[labels[k]]==undefined)
                        colors.push('#'+intToRGB(hashCode(labels[k]+'asd')))
                    else
                        colors.push(colorsC[labels[k]])
                }
                console.log(tmpData);
                var myChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: ms,

                        datasets: [{
                            backgroundColor: colors,
                            data: in_usd
                        }]
                    }
                });
            }

        });
    }
    function hashCode(str) { // java String#hashCode
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        return hash;
    }

    function intToRGB(i){
        var c = (i & 0x00FFFFFF)
            .toString(16)
            .toUpperCase();

        return "00000".substring(0, 6 - c.length) + c;
    }
</script>