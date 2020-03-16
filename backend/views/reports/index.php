<?php
use yii\web\View;
$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
$this->registerJsFile(
    '@web/js/reports.js',
    ['depends' => [\yii\web\JqueryAsset::className()],'position'=>View::POS_END]
);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.en-GB.min.js" integrity="sha256-zWVLv9rjdSAUVWhtqJUdGV1O5ONXpXMEJsOkp7B2gZ4=" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha256-jO7D3fIsAq+jB8Xt3NI5vBf3k4tvtHwzp8ISLQG4UWU=" crossorigin="anonymous" />
<div class="row">
    <div class="col-md-4">
        <div id="sandbox-container">
            <div class="input-daterange input-group" id="datepicker">
                <input type="text" class="input-sm form-control" name="start" value="<?= $date_start?>"/>
                <span class="input-group-addon">to</span>
                <input type="text" class="input-sm form-control" name="end" value="<?= $date_end?>"/>
            </div>
        </div>
    </div>
    <div class="col-md-2"><button class="btn btn-primary" onclick="apply()">Apply</button></div>
</div>
<pre>
    <?php
    print_r($forecasts);
    ?>
</pre>
<div class="row">
    <?php foreach ($orders as $order){ ?>
        <?php $color=$order->profit_based_on_bag_ap>0?'lime':'red'?>
        <?php
        $color="";
        $icon="";
        if($order->start_rate){
            if($order->sell){
                if($order->start_rate>$order->rate) {
                    $color="red";
                    $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                }else{
                    $color="lime";
                    $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                }
            }else{
                if($order->start_rate>$order->rate) {
                    $color="lime";
                    $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
                }else{
                    $color="red";
                    $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
                }
            }

        }
        ?>

        <div class="col-xs-12 col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="row">
                        <div class="col-xs-7 "><?= $order->currency_one_string.$order->currency_two_string?> / <?= $order->sell?'<b><span style="color: orange">SELL</span></b>':'<b><span style="color: purple">BUY</span></b>' ?></div>
                        <div class="col-xs-5 text-right"><?= $markets[$order->market_id]->name?></div>
                    </div>
                </div>
                <div class="panel-body">
                    <p>
                    <div class="col-xs-7">USD amount:</div>
                    <div class="col-xs-5"><?= number_format($order->usd_value,2)?></div>
                    </p>
                    <p>
                    <div class="col-xs-7">Amount:</div>
                    <div class="col-xs-5"><?= number_format($order->tokens_count,4)?></div>
                    </p>

                    <p>
                    <div class="col-xs-7">Fee:</div>
                    <div class="col-xs-5"><?= number_format($order->usd_value/100*0.2,2)?></div>
                    </p>
                    <p>
                    <div class="col-xs-7">Average Rate:</div>
                    <div class="col-xs-5"><?= number_format(($order->start_rate+$order->rate)/2,4)?><?= $icon ?></div>
                    </p>
                    <p>
                    <div class="col-xs-7">Historical profit:</div>
                    <div class="col-xs-5">???</div>
                    </p>
                    <p>
                    <div class="col-xs-7">Profit Based on Bag AR:</div>
                    <?php
                        $avg_rate=$order->sell?$order->rate/(($order->start_rate+$order->rate)/2):$order->start_rate/(($order->start_rate+$order->rate)/2);
                    ?>
                    <div class="col-xs-5"><?= number_format($avg_rate*$order->profit_based_on_bag_ap,2) ?></div>
                    </p>
                    <p>
                        <div class="dropdown">
                            <div class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                more <i class="fa fa-caret-down"></i>
                            </div>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <p>
                                <div class="col-xs-6">Date:</div>
                                <div class="col-xs-6"><?= date('Y-m-d H:i',$order->created_at)?> - <?= date('Y-m-d H:i',$order->closed_at)?></div>
                                </p>
                                <p>
                                <div class="col-xs-6">Rates:</div>
                                <div class="col-xs-6"><?= number_format($order->start_rate,4)?> - <?= number_format($order->rate,4)?> <?= $icon ?></div>
                                </p>
                            </div>
                        </div>

                    </p>


                </div>
                <div class="panel-footer">Profit: <span style="color:<?=$color?>;"><?= number_format($order->profit_based_on_bag_ap,2)?>$</span> (<?= number_format($order->profit_based_on_bag_ap/$order->usd_value,2)?>%)</div>
            </div>
        </div>
    <?php } ?>
</div>
<style>
    .datepicker.dropdown-menu {
        background-color: #1b1b1b;
    }
</style>
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
    .dropdown-item{
        display: block;
    }
    .dropdown-menu {
        background: #222424;
        padding: 5px;
        border: 1px solid;
    }
</style>