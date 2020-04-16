<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 13.03.2020
 * Time: 13:46
 */
use yii\web\View;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<link rel="stylesheet" href="/css/horizontal.css">

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js"></script>
<script src="https://www.chartjs.org/samples/latest/utils.js"></script>
<script src="https://darsa.in/sly/examples/js/vendor/plugins.js"></script>
<script src="https://darsa.in/sly/js/sly.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-annotation/0.5.7/chartjs-plugin-annotation.min.js"
        integrity="sha256-Olnajf3o9kfkFGloISwP1TslJiWUDd7IYmfC+GdCKd4=" crossorigin="anonymous"></script>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>


<h2>Possibility</h2>
<div class="main-chart" style="min-height: 400px">

</div>

<h2>Таблица над таблицей</h2>
<div class="row">
    <table class="table">
        <thead>
        <tr>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1 %</td>
            <td>колонка1 %</td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>2020-01-1 - 2020-03-20</td>
            <td>ustd +
                btc +
                BNB +
                etx +
                trx +
            </td>
            <td>колонка1</td>
            <td>колонка1 %</td>
            <td>колонка1 %</td>
        </tr><tr>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1 %</td>
            <td>колонка1 %</td>
        </tr><tr>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1 %</td>
            <td>колонка1 %</td>
        </tr><tr>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1</td>
            <td>колонка1 %</td>
            <td>колонка1 %</td>
        </tr>
        </tbody>
    </table>
</div>
<h2>Profits</h2>
<div class="row">
    <table class="table profit-table">
        <thead>
        <tr>
            <td>action</td>
            <td>day</td>
            <td>symbol</td>
            <td>twitch %</td>
            <td>profit %</td>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<h2>Trading Statistics</h2>
<canvas id="canvas"></canvas>
<h2>Profit Statistics</h2>
<canvas id="canvas2"></canvas>
<h2>Market Statistics</h2>
<div class="row">
    <div class="col-md-12">
        <div class="frame" id="basic" style="overflow: hidden;">
            <ul class="clearfix" style="transform: translateZ(0px) translateX(-684px); width: 6840px;">
                <li>
                    <span>Jan 2020</span>
                    <ul>
                        <li>ETHBTC: -0.2%</li>
                        <li>TRXBTC: -0.7%</li>
                    </ul>
                </li>
                <li>
                    <span>Feb 2020</span>
                    <ul>
                        <li>ETHBTC: 0.1%</li>
                        <li>TRXBTC: -0.2%</li>
                        <li>XRPBTC: 1.1%</li>
                        <li>XMRBTC: -0.4%</li>
                        <li>ATOMBTC: 0.6%</li>
                    </ul>
                </li>
                <li>
                    <span>Mar 2020</span>
                    <ul>
                        <li>ETHBTC:  1.2%</li>
                        <li>TRXBTC:  -0.6%</li>
                        <li>XRPBTC:  1.3%</li>
                        <li>XMRBTC:  2.2%</li>
                        <li>ATOMBTC:  0.1%</li>
                        <li>ZECBTC:  0.6%</li>
                        <li>BNBBTC:  0.7%</li>
                        <li>BCHBTC:  -0.1%</li>
                        <li>LTCBTC:  0.4%</li>
                        <li>ATOMBTC:  0.2%</li>
                        <li>XZTBTC:  1.3%</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<h2>Patch notes</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">03 Jan 2020</div>
                    <div class="col-xs-5 text-right">v.2.3.15</div>
                </div>
            </div>
            <div class="panel-body">
                <b>CoinMarketCap & 3-rd party statistic</b>
                <ul>
                    <li>Dynamic Coin Statistics</li>
                    <li>Updated UI for markets</li>
                    <li>Charts for statistics (UI)</li>
                </ul>
             </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">12 Jan 2020</div>
                    <div class="col-xs-5 text-right">v.2.4.1</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Creacting of demo version</b>
                <ul>
                    <li>Demo Orders functionality</li>
                    <li>Demo Balance calculating</li>
                    <li>Demo Trades</li>
                    <li>Playground for strategy training</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">19 Jan 2020</div>
                    <div class="col-xs-5 text-right">v.2.4.2</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Creacting of demo version</b>
                <ul>
                    <li>Bugs fixed</li>
                    <li>Strategy update, new exception rules</li>
                    <li>Logs functionnality</li>
                    <li>UI universal table for real orders</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">19 Jan 2020</div>
                    <div class="col-xs-5 text-right">v.2.4.3</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Creacting of demo version</b>
                <ul>
                    <li>Bugs fixed</li>
                    <li>Strategy update, new exception rules</li>
                    <li>Logs functionnality</li>
                    <li>UI universal table for real orders</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">26 Jan 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.1</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Front End</b>
                <ul>
                    <li>Interface for strategy dashboard</li>
                    <li>UI for scheduled order</li>
                    <li>Trading Pair statistics and rating</li>
                    <li>Updating of User Interface: User Balance, Order Calendar, Ratings, Statistics.</li>
                    <li>Calendar report page</li>
                    <li>Manual orders creation</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">5 Feb 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.2</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Performance update</b>
                <ul>
                    <li>Interface for strategy dashboard</li>
                    <li>UI for scheduled order</li>
                    <li>Trading Pair statistics and rating</li>
                    <li>Updating of User Interface: User Balance, Order Calendar, Ratings, Statistics.</li>
                    <li>Calendar report page</li>
                    <li>Manual orders creation</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">12 Feb 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.3</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Migration to high-power server</b>
                <ul>
                    <li>Interface for strategy dashboard</li>
                    <li>UI for scheduled order</li>
                    <li>Trading Pair statistics and rating</li>
                    <li>Updating of User Interface: User Balance, Order Calendar, Ratings, Statistics.</li>
                    <li>Calendar report page</li>
                    <li>Manual orders creation</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">19 Feb 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.4</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Statistics functions realization</b>
                <ul>
                    <li>Market statistics collector</li>
                    <li>Reports Udpate</li>
                    <li>Report pair page animation + tracking</li>
                    <li>Uncompleted orders processing.</li>
                    <li>Trading history implementation.</li>
                    <li>Depth analyzer</li>
                    <li>Home page and main menu UI update</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">26 Feb 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.5</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Reports</b>
                <ul>
                    <li>Market Overview</li>
                    <li>Forecast Overview</li>
                    <li>Trading Pair Rating</li>
                    <li>Manual Controls.</li>
                    <li>Forecast Statistics</li>
                    <li>Telegram Bot integration</li>
                    <li>Iternal Errors Tracking</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">3 Mar 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.6</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Reports</b>
                <ul>
                    <li>Core Integration</li>
                    <li>Terminal Integration</li>
                    <li>Trading Pair Rating</li>
                    <li>Test Trading round.</li>
                    <li>Forecast Statistics</li>
                    <li>Productivity update</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row">
                    <div class="col-xs-7 ">10 Mar 2020</div>
                    <div class="col-xs-5 text-right">v.2.5.7</div>
                </div>
            </div>
            <div class="panel-body">
                <b>Multiple Markets processing</b>
                <ul>
                    <li>Market Comparison  statistics</li>
                    <li>Safe trading exit</li>
                    <li>Safe orders</li>
                    <li>Account/Market individual order</li>
                    <li>Testing market impact functionality</li>
                </ul>
            </div>
            <div class="panel-footer">Status : Live</div>
        </div>
    </div>



</div>
<h2>Recent Orders</h2>
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




    </div>
    <div class="panel-footer">Profit: <span style="color:<?=$color?>;"><?= number_format($order->profit_based_on_bag_ap,2)?>$</span> (<?= number_format($order->profit_based_on_bag_ap/$order->usd_value,2)?>%)</div>
</div>
    </div>
<?php } ?>
</div>
<h2>Recent Forecasts</h2>
<div class="row">
    <?php foreach ($forecasts as $forecast){ ?>
        <div class="col-md-4">
            <?= $this->render('/partials/_forecast', ['forecast'=>$forecast], true)?>
        </div>

<?php } ?>
</div>


<br>
<br>
<br>
<br>
<div style="height: 100px"></div>
<script>
    var data=<?= json_encode($data) ?>;
    var forecast_statistics=<?= json_encode($forecast_statistics) ?>;
    var pair="BTCUSDT";
    var orders=[];//наши ордера
    var percent_drop=parseFloat(<?= $percent_drop ?>);
    var percent_bounce=parseFloat(<?= $percent_bounce ?>);
    var percent_profit=parseFloat(<?= $percent_profit ?>);
    var timeout=parseFloat(<?= $timeout ?>);

    $(function() {
        var torgi=[]
        var torgi_tmp=[]
        var gaps=[1,2,3,4,6,8,12,24]
        var gData=[];
        var tmp=undefined;

        var value_before=0;//для таблицы профитов
        var iterations=0;//для таблицы профитов
            for (const [key, value] of Object.entries(data)) {

            gData.push({x:new Date(key/1),y:parseFloat(value.open)})

            if(iterations>0){
                var date=new Date(key/1);
                if(forecast_statistics[date.toISOString().slice(0,10)]){//если мы в этот день торговали
                    var push_date=new Date(key/1)
                    //делаем масив торгов
                    //каждый элемент это серия дней
                    if(torgi_tmp.length){
                        if(Math.abs(torgi_tmp[torgi_tmp.length-1].x-push_date)>86400000){//если разрыв больше дня то добавим его как отдельную серию
                            torgi.push(torgi_tmp);
                            torgi_tmp=[];
                        }

                    }
                    torgi_tmp.push({x:push_date,y:parseFloat(value.open),flag:true});


                }

                var tmp_html='<tr data_date="'+date.toISOString().slice(0,10)+'"><td></td><td>'+date.toISOString().slice(0,10)+'</td><td></td><td>'+((value.open/value_before.open-1)*100).toFixed(2)+'%</td><td></td><tr>'
                $('.profit-table tbody').append(tmp_html)
            }
            value_before=value;
            iterations++;
        }
        //если в конце еще остались торги то добавим и их, добавляем последнюю серию торгов
        if(torgi_tmp.length){
            torgi.push(torgi_tmp);
            torgi_tmp=[];
        }
        var totalData=[
            {
                showInLegend: true,
                type: "line", //change it to line, area, column, pie, etc
                dataPoints: gData,
            },
        ];
        for(var lol=0;lol<torgi.length;lol++){
            totalData.push({
                color:'#cccccc',
                type: "line", //change it to line, area, column, pie, etc
                dataPoints: torgi[lol],
            });
        }
        for (var i=0;i<gData.length-timeout/600-1;i++){
            if(totalData.length>100*10)
                break;
            var bid_now=gData[i].y;
            for (var j=0;j<gaps.length;j++){
                if (typeof gData[i-j]=== 'undefined')
                    continue;
                var time1=gData[i-j].y;
                for(var k=j;k<gaps.length;k++){
                    if (typeof gData[i-j-k]=== 'undefined')
                        continue;
                    var time2=gData[i-j-k].y;
                    if((time2-time1)/time1>percent_drop && (bid_now-time1)/time1>percent_bounce){
                        //начинаем просчет
                        var strategyStart=[];


                        //тут показываем как зашли
                        for (var ii=i-j-k; ii<i;ii++){
                            strategyStart.push(gData[ii])
                        }
                        totalData.push({
                            color:'#cccccc',
                            type: "line", //change it to line, area, column, pie, etc
                            dataPoints: strategyStart
                        })

                        //тут показываем как играли и создаем ордер
                        var value_start=gData[i].y;//по какому курсу зашли
                        var value_current=gData[i].y;//это какой курс сейчас, в момент интерации
                        var strategyProcess=[];
                        var ending_point=i;
                        var ii_global=0;//мне это нужно знать, чтобы сказать когдая вышел из ставки, тут возможен баг
                        //когда  ставки игралась еще и еще но мы не отображаем это
                        for (var ii=i;ii<gData.length-1;ii++){// так как графики 5 мин, то разделим
                            if(ii>i+timeout/600){
                                console.log('break',ii,i,i+timeout/600+10)
                                break;
                            }

                            ii_global=ii;
                            ending_point=ii;
                            //if(typeof gData[ii].y=='undefined'){

                            //console.log(ii,gData[ii],i,gData.length );

                            value_current=gData[ii].y;
                            strategyProcess.push(gData[ii]);
                            if(value_current>value_start*(1+percent_profit))
                                break;//типо сработала ставка
                        }
                        //выберем цвет для графика в зависимосит от того выиграли  или проиграли
                        if(value_current>value_start){

                            var color='#4fcc37';


                            totalData.push({
                                color:color,
                                type: "line", //change it to line, area, column, pie, etc
                                dataPoints: strategyProcess
                            })
                            //создаем ордер
                            var order={};
                            order.pair=pair;
                            order.date_start=gData[i].x;
                            order.date_end=gData[ii_global].x;
                            order.rate_start=value_start;
                            order.rate_end=value_current;
                            order.usdt_bank=100000;
                            order.profit=(100000/value_start*value_current)-100000;
                            orders.push(order);
                        }else{
                            var color='#fb564c';
                        }

                        //i=parseInt((i+ii_global)/2);//нужнно подвинуть  цикл, так как мы уже сделали нашу стратегию
                        i=ii_global;//нужнно подвинуть  цикл, так как мы уже сделали нашу стратегию
                        //тут возможен баг описанный выше
                    }
                }
            }

        }
//        $.each(forecast_statistics, function (date, forecasts_stat) {
//            var total=0;
//            $.each(forecasts_stat, function (index, $f) {
//                var tmp_html='<tr style="display: none" class="forecast" data_date_forecast="'+date+'"><td></td><td>'+date+'</td><td>'+$f['symbol']+'</td><td></td><td>'+(($f['profit'])).toFixed(2)+'%</td><tr>'
//                $( "[data_date='"+date+"']" ).after(tmp_html)
//                total+=$f['profit'];
//            });
//            $( "[data_date='"+date+"']" ).find('td').eq(4).html(total.toFixed(2)+'%')
//            $( "[data_date='"+date+"']" ).find('td').eq(0).html('<i class="fa fa-caret-right" onclick="toggleForecasts();"></i>')
//        });


        var options = {

            toolTip:{
                shared: true,
//                content: function (e) {
//                    return "aaa";
//                }
            },
            zoomEnabled: true,
            title: {
                text: pair
            },
            axisX:{
                valueFormatString: "DD-MMM" ,
                labelAngle: -50
            },
            axisY: {
                // title: "If you need",
                // suffix: "K",
                includeZero: false
            },
            //animationEnabled: true,
            //exportEnabled: true,
            data: totalData
        };
        var chart = $(".main-chart").CanvasJSChart(options);

        //добавляем ордера
        var profit=0;
        $.each(orders, function (key, val) {
            profit+=val.profit;
            var o="<div class='order'>" +
                "<div class='col-md-2'>"+val.pair+"</div>" +
                "<div class='col-md-2'>"+timeConverter(val.date_start)+"</div>" +
                "<div class='col-md-2'>"+val.rate_start+" -->  "+val.rate_end+"</div>" +
                "<div class='col-md-2'>"+timeConverter(val.date_end)+"</div>" +
                //"<div class='col-md-2'>$"+val.usdt_bank+"</div>" +
                "<div class='col-md-2'>"+val.profit.toFixed(2)+"</div>" +
                "<div class='col-md-2'>"+(val.profit.toFixed(2)/val.usdt_bank*100).toFixed(2)+"%</div>" +
                "</div>";
            $('.orders').append(o)
        });
        $('.profit-value').text('$'+profit.toFixed(2));


        $.each(forecast_statistics, function (date, forecasts_stat) {
            var total=0;
            $.each(forecasts_stat, function (index, $f) {
                var tmp_html='<tr style="display: none" class="forecast" data_date_forecast="'+date+'"><td></td><td>'+date+'</td><td>'+$f['symbol']+'</td><td></td><td>'+(($f['profit'])).toFixed(2)+'%</td><tr>'
                $( "[data_date='"+date+"']" ).after(tmp_html)
                total+=$f['profit'];
            });
            $( "[data_date='"+date+"']" ).find('td').eq(4).html(total.toFixed(2)+'%')
            $( "[data_date='"+date+"']" ).find('td').eq(0).html('<i class="fa fa-caret-right" onclick="toggleForecasts();"></i>')
        });

    });

    function timeConverter(UNIX_timestamp){
        return UNIX_timestamp;
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var hour = a.getHours();
        var min = a.getMinutes();
        //var sec = a.getSeconds();
        var sec = '00';
        if(hour<10)
            hour='0'+hour;
        if(min<10)
            min='0'+min;
        var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
        return time;
    }
    function toggleForecasts(){
        var tr=$(event.srcElement).parents('tr')
        var date=$(tr).attr('data_date');
        var forecasts=$('.forecast[data_date_forecast="'+date+'"]')
        console.log(forecasts)
        forecasts.toggle()

    }
</script>
<script>


    var MONTHS = ['January 2020', 'February 2020', 'March 2020'];
    var successful = [45, 95, 121];
    var failed = [31, 41, 49];
    var declined = [4, 42, 60];
    var AvgProfit = [-1.5, 0.33, 2.1]
    var config = {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Failed Orders',
                fill: true,
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: failed,
            },
                {
                    label: 'Declineds',
                    fill: true,
                    backgroundColor: window.chartColors.grey,
                    borderColor: window.chartColors.grey,
                    data: declined,
                },
                {
                    label: 'Successful Orders',
                    backgroundColor: window.chartColors.blue,
                    borderColor: window.chartColors.blue,
                    data: successful,
                    fill: true,
                }]
        },

        options: {
            annotation: {
                annotations: [{
                    type: 'line',
                    mode: 1,
                    scaleID: 'x-axis-0',
                    value: 1.2,
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 4,
                    label: {
                        enabled: true,
                        content: 'Update 2.2.12'
                    }
                }]
            },
            responsive: true,
            title: {
                display: true,
                text: 'Mothly statistics'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },

            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    stacked: true
                }]
            }
        }
    };
    var config2 = {
        type: 'line',
        data: {
            labels: MONTHS,
            datasets: [{
                label: 'Failed Orders',
                fill: true,
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: AvgProfit,
            }
            ]
        },
        plugins: [{
            beforeRender: function (x, options) {
                var c = x.chart
                var dataset = x.data.datasets[0];
                var yScale = x.scales['y-axis-0'];
                var yPos = yScale.getPixelForValue(0);

                var gradientFill = c.ctx.createLinearGradient(0, 0, 0, c.height);
                gradientFill.addColorStop(0, 'green');
                gradientFill.addColorStop(yPos / c.height - 0.01, 'green');
                gradientFill.addColorStop(yPos / c.height + 0.01, 'red');
                gradientFill.addColorStop(1, 'red');

                var model = x.data.datasets[0]._meta[Object.keys(dataset._meta)[0]].dataset._model;
                model.backgroundColor = gradientFill;
            }
        }],
        options: {
            annotation: {
                annotations: [{
                    type: 'line',
                    mode: 1,
                    scaleID: 'x-axis-0',
                    value: 1.2,
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 4,
                    label: {
                        enabled: true,
                        content: 'Update 2.2.12'
                    }
                }]
            },
            responsive: true,
            title: {
                display: true,
                text: 'Mothly statistics'
            },
            tooltips: {
                mode: 'index',
                intersect: false,
            },

            scales: {
                xAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Month'
                    }
                }],
                yAxes: [{
                    display: true,
                    scaleLabel: {
                        display: true,
                        labelString: 'Value'
                    },
                    stacked: true
                }]
            }
        }
    };
    window.onload = function () {
        var ctx = document.getElementById('canvas').getContext('2d');
        var ctx2 = document.getElementById('canvas2').getContext('2d');
        window.myLine = new Chart(ctx, config);
        window.myLine = new Chart(ctx2, config2);

        (function () {
            var $frame = $('#basic');
            var $slidee = $frame.children('ul').eq(0);
            var $wrap = $frame.parent();

            // Call Sly on frame
            $frame.sly({
                horizontal: 1,
                itemNav: 'basic',
                smart: 1,
                activateOn: 'click',
                mouseDragging: 1,
                touchDragging: 1,
                releaseSwing: 1,
                startAt: 3,
                scrollBar: $wrap.find('.scrollbar'),
                scrollBy: 1,
                pagesBar: $wrap.find('.pages'),
                activatePageOn: 'click',
                speed: 300,
                elasticBounds: 1,
                easing: 'easeOutExpo',
                dragHandle: 1,
                dynamicHandle: 1,
                clickBar: 1,

                // Buttons
                forward: $wrap.find('.forward'),
                backward: $wrap.find('.backward'),
                prev: $wrap.find('.prev'),
                next: $wrap.find('.next'),
                prevPage: $wrap.find('.prevPage'),
                nextPage: $wrap.find('.nextPage')
            });

            // To Start button
            $wrap.find('.toStart').on('click', function () {
                var item = $(this).data('item');
                // Animate a particular item to the start of the frame.
                // If no item is provided, the whole content will be animated.
                $frame.sly('toStart', item);
            });

            // To Center button
            $wrap.find('.toCenter').on('click', function () {
                var item = $(this).data('item');
                // Animate a particular item to the center of the frame.
                // If no item is provided, the whole content will be animated.
                $frame.sly('toCenter', item);
            });

            // To End button
            $wrap.find('.toEnd').on('click', function () {
                var item = $(this).data('item');
                // Animate a particular item to the end of the frame.
                // If no item is provided, the whole content will be animated.
                $frame.sly('toEnd', item);
            });

            // Add item
            $wrap.find('.add').on('click', function () {
                $frame.sly('add', '<li>' + $slidee.children().length + '</li>');
            });

            // Remove item
            $wrap.find('.remove').on('click', function () {
                $frame.sly('remove', -1);
            });
        }());
    };


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
    .panel-body ul{
        margin-left: 30px;
    }
</style>

