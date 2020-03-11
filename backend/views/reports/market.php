
<?php
use yii\web\View;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
function ShortNumberFormat( $n, $precision = 1 ) {
    if ($n < 900) {
        // 0 - 900
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        // 0.9k-850k
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        // 0.9m-850m
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        // 0.9b-850b
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        // 0.9t+
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
    // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
    // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if ( $precision > 0 ) {
        $dotzero = '.' . str_repeat( '0', $precision );
        $n_format = str_replace( $dotzero, '', $n_format );
    }
    return $n_format . $suffix;
}
$repeats=[];
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>
<h3>Next Update in <span class="update-timer" style="font-weight: 700">30</span></h3>
<div class="row">
    <div class="col-md-2">
        <div class="panel panel-default">
            <div class="panel-heading"><?=  date("H:i",$predictions_backend->timestamp_end)?></div>
            <div class="panel-body"><?= $predictions_backend->successfully?>/<?= $predictions_backend->total?></div>
            <div class="panel-footer"><?= number_format($predictions_backend->percent,2)?> % </div>
        </div>
    </div>
    <?php
    $t=0;
    $g=0;
    $b=0;
    foreach ($pairs as $pair){
        $t++;
        if($pair->currency_group==0)
            $g++;
            else
                $b++;
    }
    ?>
    <div class="col-md-4">
        <table>
            <tr>
                <td>x5 Pairs</td>
                <td><?=$g?></td>
            </tr>
            <tr>
                <td>Exotic Pairs</td>
                <td><?=$b?></td>
            </tr>
            <tr>
                <td>Total Pairs</td>
                <td><?=$t?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-4">
        <table>
            <tr>
                <td></td>
                <td>Successfully</td>
                <td>Predictions</td>
                <td>Percent</td>
            </tr>
            <tr>
                <td>Hour</td>
                <td>27</td>
                <td>40</td>
                <td>0.05%</td>
            </tr>
            <tr>
                <td>Day</td>
                <td>637</td>
                <td>928</td>
                <td>0.94%</td>
            </tr>
            <tr>
                <td>Week</td>
                <td>4012</td>
                <td>6844</td>
                <td>3.93$</td>
            </tr>
        </table>
    </div>
</div>
<div class="btn btn-primary" onclick="tableSearch('')">All</div>
<div class="btn btn-success" onclick="tableSearch('predicted')">Predicted</div>
<div class="btn btn-danger" onclick="tableSearch('failed')">Failed</div>
<table class="table-dark table " id="data-table">
    <thead>
    <tr>
        <td>Market               </td>
        <td>Symbol               </td>
        <td>Rating                </td>
        <td>24h  volume           </td>
        <td>24h volume change        </td>
        <td>Price                 </td>
        <td>Price Change%         </td>
        <td>Quote volume         </td>
        <td>QV change             </td>
        <td>price change            </td>
        <td>Prediction           </td>
        <td>Currency group           </td>
        <td>updated              </td>


        <td>Rating</td>
        <td>24h  volume</td>
        <td>24h volume change</td>
        <td>Price</td>
        <td>Price Change</td>
        <td>Quote volume</td>
        <td>QV change</td>
        <td>price change</td>
        <td>prediction</td>
        <td>result</td>
        <td>trading_pair</td>

    </tr>
    </thead>
    <tbody>
    <?php
    $min=9000;
    $max=0;
    foreach ($pairs as $pair){
        if($pair->price_change_percent>$max)
            $max=$pair->price_change_percent;
        if($pair->price_change_percent<$min)
            $min=$pair->price_change_percent;
    }
    ?>
    <?php foreach ($pairs as $pair){ ?>
        <?php
        if(strpos($pair->trading_paid,"BTC")===false) continue;

        if(floatval($pair->quote_volume)<3) continue;
        $repeats[]=['base'=>$pair->tarding_paid];
        $rating=intval(($pair->price_change_percent-$min)/($max-$min)*95+rand(-5,5)+5);

        $predictions=$pair->prediction;

        $color='white';
        $icon='';

        $color='white';
        if(isset($predictions_backend->ids->{$pair->trading_paid})){
            if(($predictions_backend->ids->{$pair->trading_paid}>0 && $pair->prediction>0) || ($predictions_backend->ids->{$pair->trading_paid}<0 && $pair->prediction<0))
                $color='lime';
            else
                $color='red';
        }

        ?>
        <tr>
            <td><img src="https://s2.coinmarketcap.com/static/img/exchanges/32x32/270.png"></td>
            <td><?= $pair->trading_paid ?> / <?= $pair->currency_group ?></td>
            <td><?= $rating ?></td>
            <td><?= ShortNumberFormat($pair->volume,4) ?></td>
            <td><?= ShortNumberFormat($pair->volume_24h_change,4) ?></td>
            <td><?= ShortNumberFormat($pair->bid,4) ?></td>
            <td><?= ShortNumberFormat($pair->price_change_percent,4) ?></td>
            <td><?= ShortNumberFormat($pair->quote_volume,2) ?></td>
            <td><?= ShortNumberFormat($pair->quote_volume_24h_change,2) ?></td>
            <td><?= ShortNumberFormat($pair->price_change,2) ?></td>
            <td>
                <span style="color: <?=$color?>"><?= $pair->prediction>0?'<i  style="color: '.$color.'" class="fa fa-arrow-up"></i> '.ShortNumberFormat($pair->prediction,2):'<i style="color: '.$color.'" class="fa fa-arrow-down"></i> '.ShortNumberFormat(abs($pair->prediction),2) ?><?= $icon ?></span></td>
            <td><?= $pair->currency_group ?></td>
            <td><?= rand(0,1)? 'Recently':rand(2,20).' sec ago'?></td>

            <td><?= $rating ?></td>
            <td><?= $pair->volume ?></td>
            <td><?= $pair->volume_24h_change ?></td>
            <td><?= $pair->bid ?></td>
            <td><?= $pair->price_change_percent ?></td>
            <td><?= $pair->quote_volume ?></td>
            <td><?= $pair->quote_volume_24h_change?></td>
            <td><?= $pair->price_change ?></td>
            <td><?= $pair->prediction ?></td>
            <td>
                <?php if(isset($predictions_backend->ids->{$pair->trading_paid})){
                    if(($predictions_backend->ids->{$pair->trading_paid}>0 && $pair->prediction>0) || ($predictions_backend->ids->{$pair->trading_paid}<0 && $pair->prediction<0))
                        echo 'predicted';
                    else
                        echo 'failed';
                }

                ?>
            </td>
            <td><?= $pair->trading_paid ?></td>
        </tr>
    <?php } ?>
    </tbody>

</table>
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
<script>
    var table
    $(document).ready(function() {


        table=$('#data-table').DataTable({
            "order": [[ 16, "desc" ]],
            "pageLength": 50,
            "columnDefs": [
                {
                    "targets": [3,4,9,11,12,13,14,15,16,17,18,19,20,21,22,23],
                    "visible": false,
                },
            ]
        } );

        //setInterval(updateRating,3000)
        setInterval(requestUpdate,30000)
        setInterval(function () {
            $('.update-timer').html(parseInt($('.update-timer').html())-1);
        },1000)
        //setInterval(drawTable,3000)
    } );
    function tableSearch(string) {
        table.column(22).search(string).draw();
    }

    function requestUpdate() {
        $.ajax({
            type : 'POST',
            url : '/reports/prediction-update',
            data : {}
        }).done(function (predictions) {
            $('.panel-heading').html(dateFormat('H:i',new Date(predictions.timestamp_end)));
            $('.panel-body').html(predictions.successfully+"/"+predictions.total);
            $('.panel-footer').html(predictions.percent.toFixed(2)+"%");
            $.ajax({
                type : 'POST',
                url : '/reports/market-update',
                data : {}
            }).done(function(data) {

                //све пары что есть
                for(var i=0;i<data.length;i++){
                    var pair=data[i];
                    //отсеем те у которых орборот меньше  1000000
                    if(pair.value<1000000) continue;
                    if(!pair.trading_paid.includes('BTC')) continue;

                    //найлем эту валюту и обновим
                    for(var j=0;j<table.rows().count();j++){
                        var row=table.row(j).data();
                        if(row[23]==pair.trading_paid){
                            var offset=11;
                            var rating=parseInt(row[2+offset]);
                            rating+=randomIntFromInterval(-1,1);

                            rating=rating>100?100:rating;
                            rating=rating<0?0:rating;

                            row[2]=textStyle(row[2+offset],rating);
                            row[2+offset]=rating;

                            row[3]=textStyle(row[3+offset],pair.volume);
                            row[3+offset]=pair.volume;

                            row[4]=textStyle(row[4+offset],pair.volume_24h_change);
                            row[4+offset]=pair.volume_24h_change;

                            row[5]=textStyle(row[5+offset],pair.bid);
                            row[5+offset]=pair.bid;

                            row[6]=textStyle(row[6+offset],pair.price_change_percent);
                            row[6+offset]=pair.price_change_percent;

                            row[7]=textStyle(row[7+offset],pair.quote_volume);
                            row[7+offset]=pair.quote_volume;

                            row[8]=textStyle(row[8+offset],pair.quote_volume_24h_change);
                            row[8+offset]=pair.quote_volume_24h_change;

                            row[9]=textStyle(row[9+offset],pair.price_change);
                            row[9+offset]=pair.price_change;


                            if(predictions.ids[pair.trading_paid]!=undefined){
                                if((predictions.ids[pair.trading_paid]>0 && pair.prediction>0) || (predictions.ids[pair.trading_paid]<0 && pair.prediction<0)){
                                    row[10]=pair.prediction>0?'<span style="color: lime"><i  style="color: lime" class="fa fa-arrow-up"></i> '+pair.prediction.toFixed(2):'<span style="color: lime"><i  style="color: lime" class="fa fa-arrow-down"></i> '+Math.abs(pair.prediction).toFixed(2)+"</span>";
                                    row[10+offset]=pair.prediction;
                                    row[22]='predicted';
                                }else{
                                    row[10]=pair.prediction>0?'<span style="color: red"><i class="fa fa-arrow-up" style="color: red" ></i> '+pair.prediction.toFixed(2):'<span style="color: red"><i style="color: red" class="fa fa-arrow-down"></i> '+Math.abs(pair.prediction).toFixed(2)+"</span>";
                                    row[10+offset]=pair.prediction;
                                    row[22]='failed';
                                }

                            }else{
                                row[10]=pair.prediction>0?'<i class="fa fa-arrow-up"></i> '+pair.prediction.toFixed(2):'<i class="fa fa-arrow-down"></i> '+Math.abs(pair.prediction).toFixed(2);
                                row[10+offset]=pair.prediction;
                                row[22]='';
                            }


                            row[12]='Recently';

                            table.row(j).data(row);
                            break
                        }
                    }
                    table.draw();
                    $('.update-timer').html("30");
                }
            })
        });

    }
    function textStyle(old_value,new_value,icon=true) {
        var color=parseFloat(old_value)<parseFloat(new_value)?'lime':'red';
        if(parseFloat(old_value)==parseFloat(new_value)){
            color='white'
        }
        var icon=parseFloat(old_value)<parseFloat(abbreviateNumber(new_value))?'<i class="fa fa-caret-up"></i>':'<i class="fa fa-caret-down"></i>';
        if(color=='white')
            icon='';
        return  "<span style='color:"+color+"'>"+abbreviateNumber(new_value)+" "+icon+"</span>"
    }
    function updateRating() {
        for(var i=0;i<table.rows().count();i++){
            var row=table.row(i).data();
            var rating=parseInt(row[2]);
            rating+=randomIntFromInterval(-1,1);

            rating=rating>100?100:rating;
            rating=rating<0?0:rating;
            row[2]=rating;
            table.row(i).data(row);
        }
        table.draw();
    }
    function drawTable(){
        table.draw();
    }
    function randomIntFromInterval(min, max) { // min and max included
        return Math.floor(Math.random() * (max - min + 1) + min);
    }
    var SI_SYMBOL = ["", "k", "M", "B", "T", "P", "E"];

    function abbreviateNumber(number){

        // what tier? (determines SI symbol)
        var tier = Math.log10(number) / 3 | 0;

        // if zero, we don't need a suffix
        if(tier == 0) return number.toFixed(2);

        // get suffix and determine scale
        var suffix = SI_SYMBOL[tier];
        var scale = Math.pow(10, tier * 3);


        // scale the number
        var scaled = number / scale;

        // format number and add suffix
        if(SI_SYMBOL[tier]!=undefined)
        return scaled.toFixed(1) + suffix;
        else
            return scaled.toFixed(2)
    }
</script>
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