<?php

use yii\widgets\DetailView;

function removeqsvar($url, $varname) {
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    unset($qsvars[$varname]);
    $newqs = http_build_query($qsvars);
    return $urlpart . '?' . $newqs;
}
?>
<div class="col-xs-12">
    <table class="table">
        <tr>
            <td></td>
            <td>time frame</td>
            <td>total</td>
            <td>started</td>
            <td>finished</td>
            <td>not started</td>
            <td>successful</td>
            <td>profit</td>
        </tr>
        <?php foreach ($statistics as $symbol=>$stat){?>
            <tr>
                <td rowspan="3"><a href="/reports/forecast/<?=$symbol ?>"><?=$symbol ?></a></td>
                <td>Hour</td>
                <td><?=$stat->hour->total?></td>
                <td><?=$stat->hour->started?></td>
                <td><?=$stat->hour->finished?></td>
                <td><?=$stat->hour->not_started?></td>
                <td><?=$stat->hour->successful?></td>
                <td><?= number_format($stat->hour->profit,2)?>%</td>
            </tr>
            <tr>
                <td>Day</td>
                <td><?=$stat->day->total?></td>
                <td><?=$stat->day->started?></td>
                <td><?=$stat->day->finished?></td>
                <td><?=$stat->day->not_started?></td>
                <td><?=$stat->day->successful?></td>
                <td><?= number_format($stat->day->profit,2)?>%</td>
            </tr>
            <tr>
                <td>Week</td>
                <td><?=$stat->week->total?></td>
                <td><?=$stat->week->started?></td>
                <td><?=$stat->week->finished?></td>
                <td><?=$stat->week->not_started?></td>
                <td><?=$stat->week->successful?></td>
                <td><?= number_format($stat->week->profit,2)?>%</td>
            </tr>
        <?php } ?>

    </table>
</div>
<style>
    .log-wrapper{
        max-width: 30vw;
    }
    .table{
        background: #212529 !important;
        color: white !important;
    }
    td.break{
        word-break:break-all;
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
    thead tr{
        background: dimgrey;
    }
</style>