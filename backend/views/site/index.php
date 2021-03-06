<?php

use yii\web\JqueryAsset;
use yii\web\View;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;">Trade statistic:</h3>
<h1>Markets</h1>
<hr>
<div class="row">
    <div class="col-md-8">
        <div class="trade-blocks">
            <? foreach($markets as $m): ?>
                <?php if(in_array($m->id,[1,2])) continue; ?>
                <div class="col-md-4">
                    <a href="/market/<?=$m->id;?>" style="display:block;text-decoration:none;">
                        <div class="status green"></div>

                        <img src="<?=$m->image;?>" class="market-logo">

                        <table>
                            <?php if(!empty($m->statistics->now) && floatval($m->statistics->now->usdt_balance)>0){ ?>
                                <tr>
                                    <td>$</td>
                                    <td><?= number_format(($m->statistics->now->usdt_balance),2) ?></td>
                                    <td><?php if(!empty($m->statistics->day_ago)){ echo number_format(($m->statistics->now->usdt_balance-$m->statistics->day_ago->usdt_balance)/$m->statistics->now->usdt_balance,2); }?></td>
                                </tr>


                                <tr>

                                    <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                                    <td><?= number_format(0,2) ?></td>
                                    <!--<td><?= number_format($m->statistics->now->{'24h_usdt'},2) ?></td>-->
                                    <td><?php if(!empty($m->statistics->day_ago)){ echo number_format(0,2); }?></td>
                                    <!--<td><?php if(!empty($m->statistics->day_ago)){ echo number_format(($m->statistics->now->{'24h_usdt'}-$m->statistics->day_ago->{'24h_usdt'})/$m->statistics->now->{'24h_usdt'},2); }?></td>-->
                                </tr>
                            <?php }else{ ?>

                                <tbody><tr>
                                    <td>$</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>


                                <tr>

                                    <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                                    <td>0.00</td>
                                    <!--<td>0.00</td>-->
                                    <td>0.00</td>
                                    <!--<td>nan</td>-->
                                </tr>
                                </tbody>
                            <?php } ?>
                        </table>

                    </a>
                </div>
            <? endforeach; ?>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://assets.coingecko.com/markets/images/117/large/upbit.png?1520388800" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img  src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://api.cryptorank.io/static/img/exchanges/coinbase%20pro1551970953715.png" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://happycoin.club/wp-content/uploads/2018/04/Bithumb-logo.jpg" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://bloomchain.ru/wp-content/uploads/2019/02/kraken_bitcoin_cryptocurrency_exchange-5bfc324846e0fb0051461573.jpg" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://happycoin.club/wp-content/uploads/2018/03/Bitfinex.jpg" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://assets.coingecko.com/markets/images/9/large/bitstamp.jpg?1519627979" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://everipedia-storage.s3.amazonaws.com/ProfilePicture/en/liquid-exchange__4f346f_medium.webp" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>
            <div class="col-md-4">
                <a href="/market/2" style="display:block;text-decoration:none;">
                    <div class="status green"></div>

                    <img src="https://ru.bitcoinwiki.org/upload/ru/images/6/6c/GATE.IO.png" class="market-logo empty">
                    <table>
                        <tbody><tr>
                            <td>$</td>
                            <td>0.00</td>
                            <td>0.00</td>
                        </tr>


                        <tr>

                            <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                            <td>0.00</td>
                            <!--<td>0.00</td>-->
                            <td>0.00</td>
                            <!--<td>nan</td>-->
                        </tr>
                        </tbody>
                    </table>
                </a>
            </div>

            <!--<div><div class="status"></div></div>
            <div><div class="status"></div></div>
            <div><div class="status"></div></div>-->
        </div>
    </div>
    <div class="col-md-4">
        <canvas id="myChart" class="myChart"  style="height: 370px; width: 100%;     margin-bottom: 30px;"></canvas>
        <canvas id="myChart2" class="myChart"  style="height: 370px; width: 100%;     margin-bottom: 30px;"></canvas>
        <div class="col-md-12 text-center" >
            <script type="text/javascript" src="https://files.coinmarketcap.com/static/widget/currency.js"></script><div class="coinmarketcap-currency-widget" data-currencyid="1" data-base="USD" data-secondary="" data-ticker="true" data-rank="false" data-marketcap="false" data-volume="false" data-statsticker="true" data-stats="USD"></div></div>
    </div>
</div>

<div class="row">
    <h2>DEX</h2>
    <hr>
    <div class="col-md-8">
        <div class="trade-blocks">
            <? foreach($markets as $m): ?>
                <?php if(!in_array($m->id,[1,2])) continue; ?>
                <div class="col-md-4">
                    <a href="/market/<?=$m->id;?>" style="display:block;text-decoration:none;">
                        <div class="status green"></div>

                        <img src="<?=$m->image;?>" class="market-logo">

                        <table>
                            <?php if(!empty($m->statistics->now) && floatval($m->statistics->now->usdt_balance)>0){ ?>
                                <tr>
                                    <td>$</td>
                                    <td><?= number_format(($m->statistics->now->usdt_balance),2) ?></td>
                                    <td><?php if(!empty($m->statistics->day_ago)){ echo number_format(($m->statistics->now->usdt_balance-$m->statistics->day_ago->usdt_balance)/$m->statistics->now->usdt_balance,2); }?></td>
                                </tr>


                                <tr>

                                    <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                                    <td><?= number_format(0,2) ?></td>
                                    <!--<td><?= number_format($m->statistics->now->{'24h_usdt'},2) ?></td>-->
                                    <td><?php if(!empty($m->statistics->day_ago)){ echo number_format(0,2); }?></td>
                                    <!--<td><?php if(!empty($m->statistics->day_ago)){ echo number_format(($m->statistics->now->{'24h_usdt'}-$m->statistics->day_ago->{'24h_usdt'})/$m->statistics->now->{'24h_usdt'},2); }?></td>-->
                                </tr>
                            <?php }else{ ?>

                                <tbody><tr>
                                    <td>$</td>
                                    <td>0.00</td>
                                    <td>0.00</td>
                                </tr>


                                <tr>

                                    <td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
                                    <td>0.00</td>
                                    <!--<td>0.00</td>-->
                                    <td>0.00</td>
                                    <!--<td>nan</td>-->
                                </tr>
                                </tbody>
                            <?php } ?>
                        </table>

                    </a>
                </div>
            <? endforeach; ?>
        </div>
    </div>
    <div class="col-md-4">
        <canvas id="myChart3" class="myChart"  style="height: 370px; width: 100%;     margin-bottom: 30px;"></canvas>
        <canvas id="myChart4" class="myChart"  style="height: 370px; width: 100%;     margin-bottom: 30px;"></canvas>
        <div class="col-md-12 text-center" >
            <script type="text/javascript" src="https://files.coinmarketcap.com/static/widget/currency.js"></script><div class="coinmarketcap-currency-widget" data-currencyid="1958" data-base="USD" data-secondary="" data-ticker="true" data-rank="false" data-marketcap="false" data-volume="false" data-statsticker="true" data-stats="USD"></div></div>
</div>
<div class="col-md-12">
    </div>
<script>
    var colorsC= {
        USDT: "#0eff1a",
        BTC: "#fff236",
        ETH: "#61655d",
        TRX: "#ff3847",
        BNB: "#da9aff",
        ZEC: "#ffc69e",
        XRP: "#5494ff",
        XMR: "#ff7a39",
    };
    var trading_pairs=<?php echo json_encode($trading_pairs); ?>;
    var accounts =<?php echo json_encode($accounts); ?>;
    window.onload = function () {
        var markets =<?php echo json_encode(\yii\helpers\ArrayHelper::toArray($markets)); ?>;
        var statistics =<?php echo json_encode($markets); ?>;

        var ctx = document.getElementById("myChart").getContext('2d');
        var ctx2 = document.getElementById("myChart2").getContext('2d');
        var ctx3 = document.getElementById("myChart3").getContext('2d');
        var ctx4 = document.getElementById("myChart4").getContext('2d');
        var ms = [];
        var in_usd = [];
        for (var i = 0; i < markets.length; i++) {
            if([1,2].includes(markets[i].id)) continue;
            if (statistics[i].statistics != null) {
                in_usd.push(Math.abs(statistics[i].statistics.now.usdt_balance))
                ms.push(markets[i].name)
            }
        }
        var in_usd2 = [];
        var ms2 = [];
        for (var i = 0; i < markets.length; i++) {
            if(![1,2].includes(markets[i].id)) continue;
            if (statistics[i].statistics != null) {
                in_usd2.push(Math.abs(statistics[i].statistics.now.usdt_balance))
                ms2.push(markets[i].name)
            }
        }
        console.log(accounts);

        //график для не дексовых
        var acc_value={};
        for (var i=0;i<accounts.length;i++ ){
            if([1,2].includes(accounts[i].type)) continue;
            for (var j=0; j<accounts[i].balances[accounts[i].balances.length-1].balances.length;j++) {
                if(accounts[i].balances[accounts[i].balances.length-1].balances[j]==undefined)
                    continue;
                if(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate!= undefined){
                    if(typeof acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name] =='undefined'){
                        if((parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)>1)
                        acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name]=(
                            parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)
                    }else{
                        if((parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)>1)
                        acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name]+=(
                            parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)
                    }
                }
            }
        }
        var myChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ms,

                datasets: [{
                    backgroundColor: [
                        "#ff8000",
                        "#3498db",
                        "#95a5a6",
                        "#9b59b6",
                        "#f1c40f",
                        "#e74c3c",
                        "#34495e"
                    ],
                    data: in_usd
                }]
            }
        });


        //график для дексовых
        var acc_value2={};
        for (var i=0;i<accounts.length;i++ ){
             if(![1,2].includes(accounts[i].type)) continue;
            for (var j=0; j<accounts[i].balances[accounts[i].balances.length-1].balances.length;j++) {
                if(accounts[i].balances[accounts[i].balances.length-1].balances[j]==undefined)
                    continue;
                if(typeof acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name] =='undefined'){
                    if((parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                        +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                        *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)>1)
                        acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name]=(
                            parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)
                }else{
                    if((parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                        +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                        *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)>1)
                        acc_value[accounts[i].balances[accounts[i].balances.length-1].balances[j].name]+=(
                            parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value)
                            +parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].value_in_orders))
                            *parseFloat(accounts[i].balances[accounts[i].balances.length-1].balances[j].rate)
                }
            }
        }
        var myChart = new Chart(ctx3, {
            type: 'pie',
            data: {
                labels: ms2,

                datasets: [{
                    backgroundColor: [
                        "#ff8000",
                        "#3498db",
                        "#95a5a6",
                        "#9b59b6",
                        "#f1c40f",
                        "#e74c3c",
                        "#34495e"
                    ],
                    data: in_usd2
                }]
            }
        });



        //баласы бирж
        var labels=Object.keys(acc_value);
        var colors=[];
        for(var k=0;k<labels.length;k++){
            console.log(labels[k],hashCode(labels[k]),intToRGB(hashCode(labels[k])))
            if(colorsC[labels[k]]==undefined)
                colors.push('#'+intToRGB(hashCode(labels[k]+'asd')))
            else
                colors.push(colorsC[labels[k]])
        }
        var myChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: Object.keys(acc_value),

                datasets: [{
                    backgroundColor: colors,
                    data: Object.values(acc_value)
                }]
            }
        });

        //баласы дексовых бирж
        var labels=Object.keys(acc_value2);
        var colors=[];
        for(var k=0;k<labels.length;k++){
            console.log(labels[k],hashCode(labels[k]),intToRGB(hashCode(labels[k])))
            if(colorsC[labels[k]]==undefined)
                colors.push('#'+intToRGB(hashCode(labels[k]+'asd')))
            else
                colors.push(colorsC[labels[k]])
        }
        var myChart = new Chart(ctx4, {
            type: 'pie',
            data: {
                labels: Object.keys(acc_value2),

                datasets: [{
                    backgroundColor: colors,
                    data: Object.values(acc_value2)
                }]
            }
        });
    }
    function hashCode(str) { // java String#hashCode
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }

        return hash;
    }

    function intToRGB(i){
        var c = (i & 0x00FFFFFF)
            .toString(16)
            .toUpperCase();

        return "00000".substring(0, 6 - c.length) + c;
    }

</script>