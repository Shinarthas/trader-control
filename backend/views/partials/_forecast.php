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

<div class="panel panel-default" data_id="<?= $forecast->id ?>">
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

        <div class="col-xs-7">Date Planed:</div>
        <div class="col-xs-5"><?= $forecast->created_at?></div>

        <hr/>
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

        <div class="col-xs-5"><?= intval($forecast->timeframe/3600)?>h</div>
        </p>
        <br/>
        <hr/>
        <?php if($forecast->status==1){ ?>
            <p>
            <div class="col-xs-7">Date Started:</div>
            <div class="col-xs-5"><?= $forecast->started_at?></div>
            </p>

            <p>
            <div class="col-xs-7">Rate Start:</div>
            <div class="col-xs-5"><?= number_format($forecast->rate_start,10)?></div>
            </p>



        <?php } ?>
        <?php if($forecast->status==2){ ?>
            <p>
            <div class="col-xs-7">Date Started:</div>
            <div class="col-xs-5"><?= $forecast->started_at?></div>
            </p>
            <p>
            <div class="col-xs-7">Rate Start:</div>
            <div class="col-xs-5"><?= number_format($forecast->rate_start,10)?></div>
            </p>


            <p>
            <div class="col-xs-7">Date Finished:</div>
            <div class="col-xs-5"><?= $forecast->finished_at?></div>
            </p>
            <p>
            <div class="col-xs-7">Rate End:</div>
            <div class="col-xs-5"><?= number_format($forecast->rate_end,10)?></div>
            </p>

        <?php } ?>
        <hr/>
        <br/>

        <p>
            <div class="dropdown">
                <div class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    more <i class="fa fa-caret-down"></i>
                </div>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <p>
                    <div class="col-xs-6">Date:</div>
                    <div class="col-xs-6"><?= $forecast->created_at?> - <?= $forecast->fnished_at?></div>
                    </p>
                    <p>
                    <div class="col-xs-6">Rates:</div>
                    <div class="col-xs-6"><?= number_format($forecast->start_rate,4)?> - <?= number_format($forecast->rate,4)?> <?= $icon ?></div>
                    </p>
                </div>
            </div>
        </p>
    </div>
    <div class="panel-footer"></span>
        <div class="row">
            <div class="col-xs-7 ">Profit: <span style="color:<?=$color?>;"><?= number_format($forecast->result*100,2)?>%</div>
            <div class="col-xs-5 text-right">
                <?php
                if(is_null($forecast->started_at)){
                    echo "<b style='color: yellow'>Condition not triggered</b>" ;
                }else{
                    if($forecast->to_show==1)
                        echo "<b style='color: lime'>Triggered</b>" ;
                    if($forecast->to_show==0)
                        echo "<b style='color: RED'>Aborted</b>" ;
                }

                ?>
            </div>
        </div>


    </div>
</div>





