<?php

use yii\web\JqueryAsset;
use yii\web\View;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;">Trade statistic:</h3>
<div class="col-md-8">
    <div class="trade-blocks">
        <? foreach($markets as $m): ?>
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

                <img src="https://assets.coingecko.com/markets/images/117/large/upbit.png?1520388800" class="market-logo">
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

                <img src="https://api.cryptorank.io/static/img/exchanges/coinbase%20pro1551970953715.png" class="market-logo">
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

                <img src="https://happycoin.club/wp-content/uploads/2018/04/Bithumb-logo.jpg" class="market-logo">
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

                <img src="https://bloomchain.ru/wp-content/uploads/2019/02/kraken_bitcoin_cryptocurrency_exchange-5bfc324846e0fb0051461573.jpg" class="market-logo">
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

                <img src="https://happycoin.club/wp-content/uploads/2018/03/Bitfinex.jpg" class="market-logo">
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

                <img src="https://assets.coingecko.com/markets/images/9/large/bitstamp.jpg?1519627979" class="market-logo">
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

                <img src="https://everipedia-storage.s3.amazonaws.com/ProfilePicture/en/liquid-exchange__4f346f_medium.webp" class="market-logo">
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

                <img src="https://ru.bitcoinwiki.org/upload/ru/images/6/6c/GATE.IO.png" class="market-logo">
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
        <iframe frameBorder="0" scrolling="no" allowtransparency="0" src="https://bitcoinaverage.com/en/widgets?widgetType=price&bgcolor=#FFFFFF&bwidth=1&bcolor=#CCCCCC&cstyle=round&fsize=16px&ffamily=arial&fcolor=#000000&bgTransparent=solid&chartStyle=undefined&lastUpdateTime=block&currency0=USD&total=1" style="width:250px; height:275px; overflow:hidden; background-color:transparent !important;"></iframe>
    </div>
</div>
<div class="col-md-12">
    </div>
<script>
    var accounts =<?php echo json_encode($accounts); ?>;
    window.onload = function () {
        var markets =<?php echo json_encode(\yii\helpers\ArrayHelper::toArray($markets)); ?>;
        var statistics =<?php echo json_encode($markets); ?>;

        var ctx = document.getElementById("myChart").getContext('2d');
        var ctx2 = document.getElementById("myChart2").getContext('2d');
        var ms = [];
        var in_usd = [];
        for (var i = 0; i < markets.length; i++) {
            if (statistics[i].statistics != null) {
                in_usd.push(Math.abs(statistics[i].statistics.now.usdt_balance))
                ms.push(markets[i].name)
            }
        }
        var acc_value={};
        for (var i=0;i<accounts.length;i++ ){
            for (var j=0; j<accounts[i].balance.data.balances.length;j++) {
                if(typeof acc_value[accounts[i].balance.data.balances[j].name] =='undefined'){
                    acc_value[accounts[i].balance.data.balances[j].name]=accounts[i].balance.data.balances[j].value+accounts[i].balance.data.balances[j].value_in_orders
                }else{
                    acc_value[accounts[i].balance.data.balances[j].name]+=accounts[i].balance.data.balances[j].value+accounts[i].balance.data.balances[j].value_in_orders

                }
            }
        }
        console.log(acc_value);
        console.log(Object.keys(acc_value))
        console.log(Object.values(acc_value))


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
        var myChart = new Chart(ctx2, {
            type: 'pie',
            data: {
                labels: Object.keys(acc_value),

                datasets: [{
                    backgroundColor: [
                        "#ff8000",
                        "#3498db",
                        "#95a5a6",
                        "#9b59b6",
                        "#f1c40f",
                        "#e74c3c",
                        "#34495e",
                        "#34b95e",
                        "#3a49ae",
                        "#5449ff",
                        "#3f235e",
                        "#f0f590",
                        "#f03590",
                    ],
                    data: Object.values(acc_value)
                }]
            }
        });
    }

</script>