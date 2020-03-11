<?php


use yii\web\JqueryAsset;
use yii\web\View;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>

<h1>MENU 1</h1>
<?php
$hour_success=0;
$day_success=0;
$week_success=0;

$hour_count=0;
$day_count=0;
$week_count=0;

$hour_percent=0;
$day_percent=0;
$week_percent=0;
?>
<div class="row">
    <button class="btn btn-primary" onclick="tableSearch('')">All</button>
    <button class="btn btn-warning" onclick="tableSearch('x5')">x5</button>
    <button class="btn btn-default" onclick="tableSearch('exotic')">Exotic</button>
</div>
<table class="table " id="data-table">
    <thead>
        <tr>
            <td>symbol</td>
            <td >hour</td>
            <td  >hour</td>
            <td >day</td>
            <td  >day</td>
            <td >week</td>
            <td  >week</td>
            <td>week_total</td>
            <td>type</td>
            <td>week percent</td>
        </tr>
    </thead>
<?php foreach ($statistics as $symbol => $stat){ ?>
    <tr>
        <?php

            $hour_success+=$stat['hour']['successful'];
            $day_success+=$stat['day']['successful'];
            $week_success+=$stat['week']['successful'];

        $hour_count+=$stat['hour']['count'];
        $day_count+=$stat['day']['count'];
        $week_count+=$stat['week']['count'];

        $hour_percent+=$stat['hour']['percent'];
        $day_percent+=$stat['day']['percent'];
        $week_percent+=$stat['week']['percent'];
        ?>
        <td><a href="/reports/pair/<?= $symbol ?>"><?= $symbol ?></a></td>
        <td><?= $stat['hour']['successful'].'/'.$stat['hour']['count'] ?></td>
        <td><span style="color: <?=$stat['hour']['percent']>0?'lime':'red'?>;"><?= number_format($stat['hour']['percent'],2)  ?></span></td>

        <td><?= $stat['day']['successful'].'/'.$stat['day']['count'] ?></td>
        <td><span style="color: <?=$stat['day']['percent']>0?'lime':'red'?>;"><?= number_format($stat['day']['percent'],2)  ?></span></td>

        <td><?= $stat['week']['successful'].'/'.$stat['week']['count'] ?></td>
        <td><span style="color: <?=$stat['week']['percent']>0?'lime':'red'?>;"><?= number_format($stat['week']['percent'],2)  ?></span></td>
        <td><?= $stat['week']['count']?></td>
        <td><?= $stat['currency_group']?'exotic':'x5'?></td>
        <td><?= number_format($stat['week']['percent'],2)  ?></td>
    </tr>
<?php } ?>
    <tfoot>
    <tr>
        <td>TOTAL</td>
        <td><?= $hour_success.'/'.$hour_count ?></td>
        <td><span style="color: <?= $hour_percent>0?'lime':'red'?>;"><?= number_format($hour_percent,2)  ?></span></td>

        <td><?= $day_success.'/'.$day_count ?></td>
        <td><span style="color: <?= $day_percent>0?'lime':'red'?>;"><?= number_format($day_percent,2)  ?></span></td>

        <td><?= $week_success.'/'.$week_count ?></td>
        <td><span style="color: <?= $week_percent>0?'lime':'red'?>;"><?= number_format($week_percent,2)  ?></span></td>
        <td>0</td>
        <td></td>
    </tr>
    </tfoot>
</table>

<script>
    var table=undefined;
    $(document).ready(function() {
        table=$('#data-table').DataTable({
            "order": [[ 7, "desc" ]],
            "pageLength": 50,
            /*"columnDefs": [
                {
                    "targets": [7,8],
                    "visible": false,
                },
            ]*/
        } );
        table.draw();
        table
            .column( 7 )
            .data()
            .sort();
    });
    function tableSearch(string) {
        table.column(8).search(string).draw();
    }

</script>

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
    thead tr,tfoot tr{
        background: dimgrey;
    }
</style>