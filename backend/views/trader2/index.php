<?php
/* @var $this yii\web\View */
/* @var $possibility array */
/* @var $companies array */
/* @var $period int */
/* @var $trading_pairs array */
/* @var $balances array */
/* @var $top_currencies array */
/* @var $markets array */

use common\components\ApiRequest;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>




<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<div class="row">
    <div class="col-xs-12">
        <h1>Period. Started at <?= date('m-d H:i',$period)?> Ends at <?= date('m-d H:i',$period+6*3600)?> </h1>
    </div>
</div>
<div class="row">
    <?php foreach ($top_currencies as $cmc_currency){ ?>
        <div class="col-xs-12">
            <div class="col-xs-2"><img src="https://s2.coinmarketcap.com/static/img/coins/32x32/<?= $cmc_currency->id ?>.png"></div>
            <div class="col-xs-2"><?= $cmc_currency->symbol;?></div>
            <div class="col-xs-3 <?= $cmc_currency->quote->USD->percent_change_1h>0?'text-success':'text-danger' ?>">
                <?php if($cmc_currency->quote->USD->percent_change_1h>0){ echo '<i class="fa fa-angle-up"></i>';}else{ echo '<i class="fa fa-angle-down"></i>';} ?>
                <?= $cmc_currency->quote->USD->percent_change_1h;?>
            </div>
            <div class="col-xs-2"><?= number_format($cmc_currency->quote->USD->volume_24h,2);?></div>
            <div class="col-xs-2">
                <?php $markets_info=\common\assets\CoinMarketCapApi::info($cmc_currency->id) ?>
                <?php foreach ($markets_info->data->market_pairs as $market){ ?>
                    <?php foreach ($markets as $m){ ?>
                        <?php
                            if($market->exchange->name==$m->name)
                                echo "<img src='https://s2.coinmarketcap.com/static/img/exchanges/32x32/".$market->exchange->id.".png'>";
                        ?>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="col-xs-1">
                <?=  number_format($cmc_currency->quote->USD->volume_24h*0.1*($cmc_currency->quote->USD->percent_change_1h),2)  ?>
            </div>

        </div>
    <?php } ?>
</div>
<div class="row" >
    <div style="" class="col-md-6">
        <table style="    width: 100%;
    margin-bottom: 17px;
    border-radius: 3px;">
            <tr>
                <td>
                    <span style="color: red"><?= $order_statistics['week']['failed']?></span>/
                    <span style="color: orange"><?= $order_statistics['week']['canceled']?></span>/
                    <span style="color: lightskyblue"><?= $order_statistics['week']['completed']?></span>/
                    <span style="color: lightgreen"><?= $order_statistics['week']['open']?></span>/
                    <span style="color: white"><?= $order_statistics['week']['total']?></span>
                </td>
                <td>
                    <span style="color: red"><?= $order_statistics['day']['failed']?></span>/
                    <span style="color: orange"><?= $order_statistics['day']['canceled']?></span>/
                    <span style="color: lightskyblue"><?= $order_statistics['day']['completed']?></span>/
                    <span style="color: lightgreen"><?= $order_statistics['day']['open']?></span>/
                    <span style="color: white"><?= $order_statistics['day']['total']?></span>
                </td>
                <td>
                    <span style="color: red"><?= $order_statistics['hour']['failed']?></span>/
                    <span style="color: orange"><?= $order_statistics['hour']['canceled']?></span>/
                    <span style="color: lightskyblue"><?= $order_statistics['hour']['completed']?></span>/
                    <span style="color: lightgreen"><?= $order_statistics['hour']['open']?></span>/
                    <span style="color: white"><?= $order_statistics['hour']['total']?></span>
                </td>
            </tr>
            <tr>
                <td>week</td>
                <td>day</td>
                <td>hour</td>
            </tr>
            <tr>
                <td>
                    <div class="col-xs-6"><i class="fa fa-user"></i><?= $order_statistics['week']['human'] ?></div>
                    <div class="col-xs-6"><i class="fa fa-cog"></i><?= $order_statistics['week']['bot'] ?></div>
                </td>
                <td>
                    <div class="col-xs-6"><i class="fa fa-user"></i><?= $order_statistics['day']['human'] ?></div>
                    <div class="col-xs-6"><i class="fa fa-cog"></i><?= $order_statistics['day']['bot'] ?></div>
                </td>
                <td>
                    <div class="col-xs-6"><i class="fa fa-user"></i><?= $order_statistics['hour']['human'] ?></div>
                    <div class="col-xs-6"><i class="fa fa-cog"></i><?= $order_statistics['hour']['bot'] ?></div>
                </td>
            </tr>
        </table>
        <div class="row currency_wrapper">
            <?php foreach ($trading_pairs as $pair){ ?>

                <div class="col-md-6 currency currency-<?= $pair->trading_paid; ?>">
                    <div class="panel panel-default">
                        <div class="panel-body <?= $pair->trading_paid; ?>-panel">
                            <?php

                            $bid10=$pair->statistics->data->{'10min'}->bid;
                            $bid5=$pair->statistics->data->{'5min'}->bid;
                            $bid_now=$pair->statistics->data->{'now'}->bid;
                            $rating=(($bid_now-$bid5)/$bid5)+($bid10-$bid5)/$bid5*100;
                            ?>
                            <p class="symbol">
                                <?= $pair->trading_paid; ?> :
                                <span class="rating"><?php echo number_format($rating,3)?></span>
                                <span class="depth-possibility">1</span>
                            </p>
                            <div class="chart" style="min-height: 100px">

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div style="" class="col-md-6">
        <div class="row">
            <?php foreach ($companies as $company){?>
                <div class="col-xs-12 campaign">
                    <a class="btn btn btn-default" href="/trader2/<?= $company->id ?>/edit"><?= $company->name ?> </a>
                    <a href="/trader2/<?=$company->id?>/orders">посмотреть ордера</a>
                    <?php $balances=$company->getBalance(); ?>

                    <?php $total_usdt=0; ?>
                    <div class="row" style="margin-top: 10px;">
                        <?php foreach ($balances as $account=>$bb){?>
                            <div class="col-md-4">
                                <div class="panel panel-default">
                                    <?php $account=\common\models\Account::findOne($account); ?>
                                    <div class="panel-heading"><?= $account['name']?></div>
                                    <div class="panel-body">
                                        <?php foreach($bb->balances as $b){ ?>
                                            <?php if($b->name=='USDT'){ ?>
                                                <p><img src="http://icons.iconarchive.com/icons/cjdowner/cryptocurrency-flat/16/Tether-USDT-icon.png"> <?= number_format($b->value,2) ?> (<?= number_format($b->value_in_orders,2) ?>)</p>
                                            <?php } ?>
                                            <?php if($b->name=='BTC'){ ?>
                                                <p><img src="http://icons.iconarchive.com/icons/cjdowner/cryptocurrency-flat/16/Bitcoin-Plus-XBC-icon.png"><?= number_format($b->value,4) ?> (<?= number_format($b->value_in_orders,4) ?>)</p>
                                            <?php } ?>
                                        <?php } ?>

                                    </div>
                                    <div class="dropdown">
                                        <div class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            more <i class="fa fa-caret-down"></i>
                                        </div>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <?php foreach($bb->balances as $b){ ?>
                                                <?php
                                                if(isset($usdt_rates[$b->currency_id]) && $usdt_rates[$b->currency_id]->rate*($b->value+$b->value_in_orders)>1)
                                                {?>
                                                    <p  class="dropdown-item" ><?=$b->name ?> <?= number_format($b->value,2) ?> (<?= number_format($b->value_in_orders,2) ?>) $<?=number_format($usdt_rates[$b->currency_id]->rate*($b->value+$b->value_in_orders),2)?></p>
                                                    <?php } ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $total_usdt+=$bb->in_usd?>

                        <?php } ?>
                    </div>

                    <p>Баланс:

                        <span style="color: lime">$<?= number_format($total_usdt,2) ?></span>
                    </p>
                    <p>баланс в начале дня
                    <?php $balances2=$company->getBalanceDate(strtotime(date('Y-m-d  00:00:00',time()))); ?>
                        <?php $total_usdt2=0; ?>
                        <?php foreach ($balances2 as $account=>$bb){?>
                        <?php $total_usdt2+=$bb->in_usd?>

                        <?php } ?>
                        <span style="color: lime">$<?= number_format($total_usdt2,2) ?></span>

                    </p>
                    <?php foreach  ( $company->accounts as $account){?>
                        <?php $account=\common\models\Account::findOne($account); ?>
                        <input type="checkbox" checked value="<?=$account->id?>"><span style="margin-right: 10px"><?=$account->name?></span>
                    <?php } ?>
                    <p>
                        <btn class="btn btn-danger" target="_blank" onclick="usdtWithAll('/trader2/<?= $company->id ?>/usdt-with-all')">Отменить все и перейти в USDT</btn>
                        <!--<a class="btn btn-danger" target="_blank" href="/trader2/<?= $company->id ?>/usdt-with-all">Отменить все и перейти в USDT</a>-->
                        <btn class="btn btn-danger" target="_blank" onclick="usdtWithAll('/trader2/<?= $company->id ?>/usdt-with-entrance')">перейти в USDT с входной валютой</btn>
                        <btn class="btn btn-danger" target="_blank" onclick="usdtWithAll('/trader2/<?= $company->id ?>/entrance-with-usdt')">Закупится основной валютой</btn>
                    </p>
                </div>

            <?php } ?>
        </div>
    </div>


</div>
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
<div class="row" >
    <?php foreach ($companies as $company){?>
        <a class="btn btn btn-default" href="/trader2/<?= $company->id ?>/edit"><?= $company->name ?> </a>
    <?php } ?>
    <a class="btn btn btn-primary" href="/trader2/new">New Campaign</a>

</div>
<style>
    .dropdown-item{
        display: block;
    }
    .dropdown-menu {
        background: #222424;
        padding: 5px;
        border: 1px solid;
    }
    .campaign{
        padding: 10px;
        border: 1px solid white;
        border-radius: 5px;
        margin: 5px;
    }


    .text-success{
        color: lime;
    }
</style>
<script>
    var trading_pairs=<?= json_encode($trading_pairs)?>;

    var balances=<?= json_encode($balances[0]->balances)?>;
</script>
<script>
    window.onload = function () {
        for(var i=0;i<trading_pairs.length;i++){
            var pair=trading_pairs[i];
            var dp=[];
            for(var j=0;j< pair.statistics.data.length-10;j+=10){
                dp.push({x:new Date(pair.statistics.data[j].created_at),y:pair.statistics.data[j].ask})
            }
            console.log(dp);
            var options = {
                theme: "dark1",
                title: {
                    text: pair.trading_paid
                },
                axisX:{
                    valueFormatString: "HH:ii" ,
                    labelAngle: -50
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
                        dataPoints: dp
                    }
                ]
            };
            if($("."+pair.trading_paid+"-panel").find('.chart').length)
                $("."+pair.trading_paid+"-panel").find('.chart').CanvasJSChart(options);
        }
        var dd=[];
        console.log(balances);
        for (const [key, value] of Object.entries(balances)) {
            dd.push({y:value.value,label:key})
        }
        console.log(dd);
        var chart = new CanvasJS.Chart("chartContainer", {
            theme: "dark1", // "light1", "light2", "dark1", "dark2"
            exportEnabled: true,
            animationEnabled: true,
            title: {
                text: "Bank"
            },

            data: [{
                type: "pie",
                showInLegend: true,
                //toolTipContent: "<b>{name}</b>: ${y} (#percent%)",
                indexLabel: "{label}",
                //legendText: "{name} (#percent%)",
                indexLabelPlacement: "inside",
                dataPoints: dd
            }]
        });
        chart.render();


    }



    function refresh() {

            //window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

    function usdtWithAll(url) {
        var accounts=[];
        var inputs=$(this.event.target).parents('.campaign').find('input:checked');
        $.each(inputs, function(){
            accounts.push($(this).val());
        });
        $.ajax({
            type : 'POST',
            url : url,
            data : {'accounts':accounts}
        }).done(function(data) {
            alert(data);
            //console.log(data)
            //location=location;
        }).fail(function() {
            // Если произошла ошибка при отправке запроса
            $("#output").text("error3");
        })
    }

</script>
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