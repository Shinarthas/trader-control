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
        <table class="table">
            <thead>
            <tr>
                <th>id</th>
                <th>date</th>
                <th>Currency</th>
                <th>rate</th>
                <th>tokens</th>
                <th>status</th>
                <th>profit</th>
                <th>period</th>
            </tr>
            </thead>
            <tbody>

            <?
            $total_profit=0;
            for($i=0;$i<count($orders);$i++): ?>
                <? $t=$orders[$i]; ?>
                <?php if($t->sell==1) continue; ?>
                <tr>
                    <td><?=$t->id;?></td>
                    <td><?=date("Y-m-d H:i:s", $t->time-2*3600);?></td>
                    <td><?=$t->currency_one;?></td>
                    <td><?php if($orders[$i+1]->sell==1 && $orders[$i+1]->currency_one==$t->currency_one) {echo $orders[$i]->rate.'->';}?><?=$orders[$i+1]->rate;?></td>
                    <td><?=$t->tokens_count;?></td>
                    <td><? if($t->status==1){echo "<b style='color:red'>error</b>";} else if($t->status==2){echo "OK";
                            if($t->progress != 100) {
                                echo '<b style="color:red"> ('.$t->progress.'%)</b>';
                            }
                        }else if($t->status==3){
                            echo "price error";
                        } else {
                            echo $t::$statuses[$t->status];
                        }

                        ?></td>
                    <td>
                        <?php if($orders[$i+1]->sell==1 && $orders[$i+1]->currency_one==$t->currency_one){

                            $money_was=$orders[$i]->rate*$orders[$i]->tokens_count;
                            $money_now=$orders[$i+1]->rate*$orders[$i]->tokens_count;
                            //echo "(".$money_was." -,- ".$money_now.")";
                            //if($t->status==\common\models\DemoTask::STATUS_CREATED)
                            //print_r($trading_pairs_remapped[$t->currency_one.$t->currency_two]->statistics->data->now->bid);
                            //$money_now=$trading_pairs_remapped[$t->currency_one.$t->currency_two]->statistics->data->now->bid*$orders[$i+1]->tokens_count;
//                            if($t->status==\common\models\DemoTask::STATUS_COMPLETED)
//                                $money_now=$t->rate+$orders[$i+1]->tokens_count;
                            $total_profit+=$perccent;
                            $perccent=($money_now-$money_was)/$money_now*100;
                            echo  number_format($perccent,2).'%';
                        }?>
                    </td>
                   <td><?php echo getPeriod($t->time-2*3600)?></td>
                </tr>

            <? endfor; ?>
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
    .symbol{
        color: black;
        font-size: 24px;
        font-weight: bold;
    }
    .rating{
        color: black;
        font-size: 12px;
    }
    .menu{
        opacity: 0;
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

            window.location.reload(true);

    }

    setTimeout(refresh, 60*1000)

</script>