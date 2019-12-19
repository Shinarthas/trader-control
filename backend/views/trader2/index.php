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
    <div style="max-height: 400px; overflow: auto" class="col-md-6">
        <div class="row">
            <?php foreach ($trading_pairs as $pair){ ?>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body <?= $pair->trading_paid; ?>-panel">
                            <p class="symbol"><?= $pair->trading_paid; ?></p>
                            <div class="chart" style="min-height: 100px">

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div style="max-height: 400px; overflow: auto" class="col-md-6">
        <div  class="row">
            <p>Totals</p>

            <div class="col-md-6">
                <?php foreach ($balances[0]->balances as $symbol=>$balance){ ?>
                    <?php if($balance['value']>0) {?>

                    <p><?= $symbol ?>: <?= $balance['tokens'] ?>(<?= $balance['value'] ?>)</p>
                    <?php } ?>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <?php foreach ($balances as $b){ ?>

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
<div class="row">
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
        </tr>
        </thead>
        <tbody>

        <? for($i=0;$i<count($orders);$i++): ?>
        <? $t=$orders[$i]; ?>
        <?php if($t->sell==0) continue; ?>
            <tr>
                <td><?=$t->id;?></td>
                <td><?=date("d/m/y H:i", $t->time);?></td>
                <td><?=$t->currency_one;?></td>
                <td><?=$t->rate;?></td>
                <td><?=$t->tokens_count;?></td>

                <td><?=$t->rate*$t->tokens_count;?></td>
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

            </tr>

        <? endfor; ?>
        <tbody>
    </table>
</div>
<div class="row">
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
            $("."+pair.trading_paid+"-panel").find('.chart').CanvasJSChart(options);
        }


    }
</script>