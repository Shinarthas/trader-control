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

