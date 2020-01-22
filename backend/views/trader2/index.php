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
        <div class="row currency_wrapper">
            <?php foreach ($trading_pairs as $pair){ ?>
                <?php if($pair->statistics->data->{'5min'}->bid==0){continue;}?>
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
                <div class="col-xs-12">
                    <a class="btn btn btn-default" href="/trader2/<?= $company->id ?>/edit"><?= $company->name ?> </a>
                    <a href="/trader2/<?=$company->id?>/orders">посмотреть ордера</a>
                    <p>Баланс:
                        <?php $balances=$company->getBalance(); ?>
                        <?php $total_usdt=0; ?>
                        <?php foreach ($balances as $account=>$bb){?>
                            <?php $total_usdt+=$bb->in_usd?>

                        <?php } ?>
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
                    <p>
                        <a class="btn btn-danger" target="_blank" href="/trader2/<?= $company->id ?>/usdt-with-all">Отменить все и перейти в USDT</a>
                        <a class="btn btn-danger" target="_blank" href="/trader2/<?= $company->id ?>/usdt-with-entrance">перейти в USDT с входной валютой</a>
                        <a class="btn btn-danger" target="_blank" href="/trader2/<?= $company->id ?>/entrance-with-usdt">Закупится основной валютой</a>
                    </p>
                </div>

            <?php } ?>
        </div>
    </div>
    <div style="" class="col-md-6">
        <div  class="row">
            <div class="col-xs-12">
                <p>Totals</p>

                <div class="col-md-6">
                    <?php foreach ($balances[0]->balances as $symbol=>$balance){ ?>
                        <?php if($balance['value']>0) {?>

                            <p><b style="font-size: 24px"><?= $symbol ?></b>: <span style="font-size: 24px; color:lime">$(<?= number_format($balance['value'],2) ?>)</span> <?= number_format($balance['tokens'],2) ?></p>
                        <?php } ?>
                    <?php } ?>

                    <div id="chartContainer" style="height: 370px; width: 100%;"></div>
                </div>
                <?php
                $moneynow=0;
                foreach ($balances[0]->balances as $bb){
                    $moneynow+=floatval($bb['value']);
                }
                $money_was=0;
                foreach ($balances[count($balances)-1]->balances as $bb){
                    $money_was+=floatval($bb['value']);
                }
                echo $money_was."<br>";
                $profit=$moneynow-$money_was;
                $percent=$profit/$money_was*100;
                ?>
                <div class="col-md-6">
                    <div><b>Profit:</b></div>
                    <div>
                        <span class="<?= $profit>0?'text-success':'text-danger' ?>" >$<?= number_format($profit,2)?></span>
                        <span class="<?= $profit>0?'text-success':'text-danger' ?>" style="font-size: 24px"><i class="fas fa-caret-square-up"></i>(<?= number_format($percent,2)?>%)</span>
                    </div>
                    <?php $prev='1970-01-01 00:00:00'; ?>
                    <?php foreach ($balances as $b){ ?>
                        <?php
                        if(abs(strtotime($prev)-strtotime($b->timestamp))<10){
                            continue;
                        }else{
                            $prev=$b->timestamp;
                        }
                        ?>
                        <p><?php echo  date('H:i',strtotime($b->timestamp)) ?> :
                            <?php


                            $total_usdt=0;
                            foreach ($b->balances as $bb){
                                $total_usdt+=$bb['value'];
                            }
                            echo $total_usdt;
                            ?>

                        </p>
                    <?php }  ?>
                </div>
            </div>

        </div>
    </div>

</div>

<div class="row" >
    <?php foreach ($companies as $company){?>
        <a class="btn btn btn-default" href="/trader2/<?= $company->id ?>/edit"><?= $company->name ?> </a>
    <?php } ?>
    <a class="btn btn btn-primary" href="/trader2/new">New Campaign</a>

</div>
<style>
    .symbol{
        color: black;
        font-size: 24px;
        font-weight: bold;
    }
    .rating,.depth-possibility{
        color: black;
        font-size: 12px;
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
            var options = {
                title: {
                    text: pair.trading_paid
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
                        dataPoints: [
                            //{ x: 0, y:  pair.statistics.data['240min'].ask},
                            //{ x: 120, y:  pair.statistics.data['120min'].ask},
                            //{ x: 180, y:  pair.statistics.data['60min'].ask},
                            { x: 200, y:  pair.statistics.data['40min'].ask},
                            { x: 210, y:  pair.statistics.data['30min'].ask},
                            { x: 220, y:  pair.statistics.data['20min'].ask},
                            { x: 225, y:  pair.statistics.data['15min'].ask},
                            { x: 230, y:  pair.statistics.data['10min'].ask},
                            { x: 235, y:  pair.statistics.data['5min'].ask },
                            { x: 240, y: pair.statistics.data['now'].ask },
                        ]
                    }
                ]
            };
            if($("."+pair.trading_paid+"-panel").find('.chart').length)
                $("."+pair.trading_paid+"-panel").find('.chart').CanvasJSChart(options);
        }
        var dd=[];
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

            window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

</script>