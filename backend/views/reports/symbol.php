<?php
use yii\web\JqueryAsset;
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

 ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>

<script type="text/javascript" src="https://cdn.datatables.net/v/bs/jqc-1.12.4/jszip-2.5.0/dt-1.10.20/af-2.3.4/b-1.6.1/b-colvis-1.6.1/b-flash-1.6.1/b-html5-1.6.1/b-print-1.6.1/cr-1.5.2/fc-3.3.0/fh-3.1.6/kt-2.5.1/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>

<h1><?= $symbol ?></h1>
<?php
$total=0;
foreach ($pairs as $pair){

    $tmp=json_decode($pair->ids);
    if(isset($tmp->{$pair->trading_paid}))
        $total+=$tmp->{$pair->trading_paid};
}

?>
<div class="row">
    <div class="col-md-4">
        <script type="text/javascript" src="https://files.coinmarketcap.com/static/widget/currency.js"></script><div class="coinmarketcap-currency-widget" data-currencyid="<?= $cmc_coin->cmc_id?>" data-base="USD" data-secondary="" data-ticker="true" data-rank="true" data-marketcap="true" data-volume="true" data-statsticker="true" data-stats="USD"></div>
    </div>
    <div class="col-md-8">
        <?php
        $pair=$pairs[0];
        ?>
        <h2>Current prediction</h2>
        <div class="col-md-3" style="font-size: 90px"> <?= $pair->prediction>0?'<i style="color: lime" class="fa fa-arrow-up"></i>':'<i style="color: red" class="fa fa-arrow-down"></i>'?></div>
        <div class="col-md-5" style="font-size: 90px"> <?= number_format(abs($pair->prediction),2)?></div>
        <div class="col-md-4" style="font-size: 90px"><span style="color: <?= $total>0?'lime':'red' ?>;"><?= number_format($total,2)?></span> </div>
    </div>
    <div class="col-md-12">
        <div class="row">
            <div class="btn btn-primary" onclick="tableSearch('')">All</div>
            <div class="btn btn-success" onclick="tableSearch('predicted')">Predicted</div>
            <div class="btn btn-danger" onclick="tableSearch('failed')">Failed</div>
        </div>
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
                <td>Timestamp              </td>


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

                $tmp=json_decode($pair->ids);


                if(isset($tmp->{$pair->trading_paid})){
                    if(($tmp->{$pair->trading_paid}>0 && $pair->prediction>0) || ($tmp->{$pair->trading_paid}<0 && $pair->prediction<0))
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
                    <td><?= $pair->created_at?></td>

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
                        <?php if(isset($tmp->{$pair->trading_paid})){
                            if(($tmp->{$pair->trading_paid}>0 && $pair->prediction>0) || ($tmp->{$pair->trading_paid}<0 && $pair->prediction<0))
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
    </div>
</div>
<script>
    var table
    $(document).ready(function() {


        table=$('#data-table').DataTable({
            //"order": [[ 12, "desc" ]],
            "pageLength": 50,
            "columnDefs": [
                {
                    "targets": [3,4,9,11,13,14,15,16,17,18,19,20,21,22,23],
                    "visible": false,
                },
            ]
        } );
    } );

    function tableSearch(string) {
        table.column(22).search(string).draw();
    }
</script>