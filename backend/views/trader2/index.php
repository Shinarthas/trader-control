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
?>




<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>
<div class="row" >
    <div style="max-height: 800px; overflow: hidden" class="col-md-6">
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
                            </p>
                            <div class="chart" style="min-height: 100px">

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div style="max-height: 800px; overflow: hidden" class="col-md-6">
        <div  class="row">
            <p>Totals</p>

            <div class="col-md-6">
                <?php foreach ($balances[0]->balances as $symbol=>$balance){ ?>
                    <?php if($balance['value']>0) {?>

                        <p><b style="font-size: 24px"><?= $symbol ?></b>: <span style="font-size: 24px; color:lime">$(<?= number_format($balance['value'],2) ?>)</span> <?= number_format($balance['tokens'],2) ?></p>
                    <?php } ?>
                <?php } ?>

                <div id="chartContainer" style="height: 370px; width: 100%;"></div>
            </div>
            <div class="col-md-6">
                <?php $prev='1970-01-01 00:00:00'; ?>
                <?php foreach ($balances as $b){ ?>
                    <?php
                    if(abs(strtotime($prev)-strtotime($b->timestamp))<10){
                        continue;
                    }else{
                        $prev=$b->timestamp;
                    }
                    ?>
                    <p><?php echo  $b->timestamp ?> :
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

<div class="row" style="display: none">
    <?php foreach ($companies as $company){?>
        <a class="btn btn btn-default" href="/trader2/<?= $company->id ?>/edit"><?= $company->name ?> </a>
    <?php } ?>
    <a class="btn btn btn-primary" href="/trader2/new">New Company</a>

</div>
<style>
    .symbol{
        color: black;
        font-size: 24px;
        font-weight: bold;
    }
    .rating{
        color: black;
        font-size: 12px;
    }
    body,html{
        overflow: hidden;
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
                    interval: 1,
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
                            { x: 1, y:  pair.statistics.data['10min'].ask},
                            { x: 2, y:  pair.statistics.data['5min'].ask },
                            { x: 3, y: pair.statistics.data['now'].ask },
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


    setInterval(updateAndSort,5000)
    function updateAndSort() {
        $('.currency_wrapper .currency').each(function( index ) {
            var rating=parseFloat($(this).find('.rating').text())
            var random=generateRandomNumber(-0.1,0.1);
            $(this).find('.rating').text((rating+random).toFixed(3))
        });
        // $('.currency_wrapper .currency').sort(function(a,b) {
        //     return parseFloat($(a).find('.rating').text()) > parseFloat($(b).find('.rating').text());
        // }).appendTo('.currency_wrapper');


        $('.currency_wrapper .currency').sort(function(a, b) {
            return +$(a).find('.rating').text() - +$(b).find('.rating').text();
        })
            .each(function() {
                $('.currency_wrapper').append(this);
            });
    }
    function generateRandomNumber(min,max) {
            highlightedNumber = Math.random() * (max - min) + min;

            return highlightedNumber;
    };
    function refresh() {

            window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

</script>