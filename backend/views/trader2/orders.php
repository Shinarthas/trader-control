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
    var trading_pairs=<?= json_encode($trading_pairs)?>
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

           // window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

</script>