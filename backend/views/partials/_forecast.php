<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 13.03.2020
 * Time: 17:54
 */
?>
<?php $color=$order->profit_based_on_bag_ap>0?'lime':'red'?>
<?php
$color="";
$icon="";
if($order->start_rate){
    if($order->sell){
        if($forecast->start_rate>$forecast->rate) {
            $color="red";
            $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
        }else{
            $color="lime";
            $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
        }
    }else{
        if($forecast->start_rate>$forecast->rate) {
            $color="lime";
            $icon="<i class=\"fa fa-caret-down\" style='color: $color'></i>";
        }else{
            $color="red";
            $icon="<i class=\"fa fa-caret-up\" style='color: $color'></i>";
        }
    }

}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-xs-7 "><?= $forecast->symbol ?> </div>
            <div class="col-xs-5 text-right">
                <?php
                if($forecast->status==0)
                    echo "<b style='color: yellow'>PLANED</b>" ;
                if($forecast->status==1)
                    echo "<b style='color: blue'>LAUNCHED</b>" ;
                if($forecast->status==2 && $forecast->result>0)
                    echo "<b style='color: lime'>FINISHED</b>" ;
                if($forecast->status==2 && $forecast->result<0)
                    echo "<b style='color: RED'>FINISHED</b>" ;
                ?>
            </div>
        </div>
    </div>
    <div class="panel-body">
        <p>
        <div class="col-xs-7">Entry1:</div>
        <div class="col-xs-5"><?= number_format($forecast->entry1,6)?></div>
        </p>
        <p>
        <div class="col-xs-7">Entry2:</div>
        <div class="col-xs-5"><?= number_format($forecast->entry2,6)?></div>
        </p>
        <p>
        <div class="col-xs-7">Exit1:</div>
        <div class="col-xs-5"><?= number_format($forecast->exit1,6)?></div>
        </p>
        <p>
        <div class="col-xs-7">Exit2:</div>
        <div class="col-xs-5"><?= number_format($forecast->exit2,6)?></div>
        </p>
        <p>
        <div class="col-xs-7">STOP:</div>
        <div class="col-xs-5"><?= number_format($forecast->entry1,6)?></div>
        </p>
        <p>
        <div class="col-xs-7">Timeframe:</div>
        <div class="col-xs-5"><?= number_format($order->timeframe,6)?></div>
        </p>
        <?php if($forecast->status==2){ ?>

        <?php } ?>
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
        <div class="col-xs-5">???AA</div>
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
    </div>
    <div class="panel-footer">Profit: <span style="color:<?=$color?>;"><?= number_format($order->profit_based_on_bag_ap,2)?>$</span> (<?= number_format($order->profit_based_on_bag_ap/$order->usd_value,2)?>%)</div>
</div>





