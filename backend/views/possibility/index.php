<?php
/* @var $this yii\web\View */
/* @var $possibility array */
/* @var $orders array */

use common\components\ApiRequest;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

?>
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
</style>
<link rel="stylesheet" href="/css/jquery.listtopie.css">

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js"></script>
<script src="/js/jquery.listtopie.min.js"></script>

<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;">Earn campaign:</h3>
<div class="row">
    <div class="col-md-4">

        <table class="table table-dark">

            <tr>
                <td></td>
                <td>rate</td>
                <td>short +</td>
                <td>long +</td>
            </tr>
            <?php foreach ($possibility as $p){?>
                <tr>
                    <td><?= \common\models\Currency::findOne($p->currency_one)->symbol?></td>
                    <td ><?php
                        $rates=ApiRequest::statistics('v1/exchange-course/get-course',
                            [
                                'market_id'=>$p->market_id,
                                'currency_one'=>$p->currency_one,
                                'currency_two'=>$p->currency_two,
                            ]);
                        echo number_format($rates->data->sell_price,3)
                        ?></td>

                    <td><?= $p->chance?></td>
                    <td><?= $p->chance+mt_rand(-8,8)?></td>
                </tr>
            <?php } ?>

        </table>
    </div>
    <div class="col-md-6">

        <h4>Estimated value: 1006.33 USDT</h4>

        <div class="row">

            <div class="col-md-6">
                <div class="five columns" style="max-width:200px;">
                    <div class='rowtest2'>
                        <div data-lcolor="#313c42">51.1<span>USDT</span></div>
                        <div data-lcolor="#ef8e39">23.2<span>BTC</span></div>
                        <div data-lcolor="#005ce6">16.3<span>ETH</span></div>
                        <div data-lcolor="#de2821">10.4<span>WIN</span></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="seven columns" style="padding-top:20px;">
                    <div class='result_list'>
                    </div>
                </div>
            </div>

        </div>

        <h4 style="margin-top:50px;">Trade history:</h4>
        <div class="col-xs-12">
            <table>
                <thead>
                <tr>
                    <th>acc</th>
                    <th>date</th>
                    <th>currency</th>
                    <th>rate</th>
                    <th>status</th>
                </tr>
                </thead>
                <tbody>

                <? for($i=0; $i<count($orders);$i++): ?>
                <?php $t=$orders[$i]; ?>
                <?php if(!$t->sell) continue;?>
                    <tr>
                        <td><?=$t->account_id;?></td>
                        <td><?=date("H:i", $t->time);?></td>
                        <td><?php print_r($t->promotion->main_currency->symbol);?></td>
                        <!--<td><?=$t->tokens_count;?></td>-->
                        <td>
                            <?php
                            $start='';
                            if($i+1<count($orders) && $orders[$i+1]->sell==0)
                            $start=number_format($orders[$i+1]->rate,3).'->';
                            echo $start.number_format($t->rate,3);
                            ?>
                        </td>
                        <td><? if($t->status==1){echo "OK<b style='color:red'>($t->progress.%)</b>";} else if($t->status==2){echo "OK";
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


    </div>
</div>

<style>
    .table{
        background: #212529 !important;
        color: white !important;
    }
    td.break{
        word-break:break-all;
    }
</style>
<script>
    $('#static').listtopie({
        startAngle:0,
        strokeWidth:0,
        hoverEvent:false,
        drawType:'round',
        speedDraw:150,
        hoverColor:'#ffffff',
        textColor:'#000',
        strokeColor:'#ffffff',
        textSize:'18',
        hoverAnimate:true,
        marginCenter:1,
        easingType:mina.bounce,
        infoText:true,
    });

    $('.rowtest2').listtopie({
        size:'auto',
        strokeWidth:2,
        hoverEvent:true,
        hoverBorderColor:'#585858',
        hoverWidth:2,
        textSize:'16',
        marginCenter:30,
        listVal:true,
        strokeColor:'#fff',
        listValMouseOver: true,
        infoText:false,
        setValues:false,
        listValInsertClass:'result_list',
        backColorOpacity: '0.8',
        hoverSectorColor:true,
        usePercent:true
    });

</script>