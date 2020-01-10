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
<div class="row">
    <div class="col-md-2">time</div>
    <div class="col-md-2">total</div>
    <div class="col-md-2">profit</div>
</div>

    <?php $b_old=1000000; ?>
<?php $i=0;

$bbb=[];
?>
<?php foreach ($balances as $b){?>
<div class="row">
        <?php

        $total_usdt=0;
        foreach ($b->balances as $bb){
            $total_usdt+=$bb['value'];
        }
        $profit=$total_usdt-$b_old;
        $b_old=$total_usdt;
        $bbb[]=$total_usdt;
        ?>
        <div class="col-md-2" style="color: lightgreen; font-size: 21px">$<?= number_format($total_usdt,2) ?></div>
        <div class="col-md-2"><?= $b->timestamp ?></div>

        <div class="col-md-2" style="color: <?= $profit>0?'lightgreen':'red'?>"><?= number_format($profit,2) ?></div>
    <?php if ($i==20){?>
        <div class="col-md-12">
            <hr>
            <div class="col-md-8"></div>
            <div class="col-md-4">Withdraw: $<?= number_format($bbb[20]-$bbb[0],2) ?>

                <a  target="_blank" href="/trader2/pdf?date_start=<?= $b->timestamp; ?>">report</a>
            </div>
        </div>

    <?php } ?>
    <?php         $i++;
    ?>
</div>
<?php } ?>


<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

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
