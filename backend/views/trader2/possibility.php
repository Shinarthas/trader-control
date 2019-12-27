<?php
/* @var $this yii\web\View */
/* @var $possibility array */
/* @var $companies array */
/* @var $period int */
/* @var $trading_pairs array */
/* @var $balances array */
/* @var $top_currencies array */
/* @var $markets array */
/* @var $pair string */
/* @var $percent_drop float */
/* @var $percent_bounce float */
/* @var $percent_profit float */
/* @var $timeout int */

use common\components\ApiRequest;
use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>




<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script src="https://canvasjs.com/assets/script/jquery.canvasjs.min.js"></script>

<div class="row">
    <div class="main-chart" style="min-height: 400px"></div>
</div>
<div class="row">
    <h1>Orders</h1>
    <div class="orders">
        <div class="col-md-2">Pair</div>
        <div class="col-md-2">Date Start</div>
        <div class="col-md-2">Open/Close Rate</div>
        <div class="col-md-2">Date End</div>
        <div class="col-md-2">Bank</div>
        <div class="col-md-2">Profit</div>
    </div>
</div>
<div class="row">
    <h1>Profit</h1>
    <div class="profit">
        <div class="col-md-10"></div>
        <div class="col-md-2 profit-value"></div>
    </div>
</div>

<script>
    var data=<?= json_encode($data) ?>;
    var orders=[];//наши ордера
    var pair='<?php echo $pair;?>';
</script>
<script>

    var percent_drop=parseFloat(<?= $percent_drop ?>);
    var percent_bounce=parseFloat(<?= $percent_bounce ?>);
    var percent_profit=parseFloat(<?= $percent_profit ?>);
    var timeout=parseFloat(<?= $timeout ?>);
    window.onload = function () {
        var gaps=[1,2,3,4,6,8,12,24]
            var gData=[];
        for (const [key, value] of Object.entries(data)) {
            gData.push({x:key/1000,y:parseFloat(value.open)})
        }
        var totalData=[
            {
                showInLegend: true,
                type: "line", //change it to line, area, column, pie, etc
                dataPoints: gData
            }
        ];
        for (var i=0;i<gData.length;i++){
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
                        for (var ii=i;ii<gData.length, ii<i+timeout/600;ii++){// так как графики 5 мин, то разделим
                            ii_global=ii;
                            ending_point=ii;
                            value_current=gData[ii].y;
                            strategyProcess.push(gData[ii]);
                            if(value_current>value_start*(1+percent_profit))
                                break;//типо сработала ставка
                        }
                        //выберем цвет для графика в зависимосит от того выиграли  или проиграли
                        if(value_current>value_start){
                            var color='#4fcc37';
                        }else{
                            var color='#fb564c';
                        }
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
                        //i=parseInt((i+ii_global)/2);//нужнно подвинуть  цикл, так как мы уже сделали нашу стратегию
                        i=ii_global;//нужнно подвинуть  цикл, так как мы уже сделали нашу стратегию
                        //тут возможен баг описанный выше
                    }
                }
            }

        }


            var options = {

                toolTip:{
                    shared: true
                },
                zoomEnabled: true,
                title: {
                    text: pair
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
                "<div class='col-md-2'>$"+val.usdt_bank+"</div>" +
                "<div class='col-md-2'>"+val.profit.toFixed(2)+"</div>" +
                "</div>";
            $('.orders').append(o)
        });
        $('.profit-value').text('$'+profit.toFixed(2));
    }
    function timeConverter(UNIX_timestamp){
        var a = new Date(UNIX_timestamp * 1000);
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var year = a.getFullYear();
        var month = months[a.getMonth()];
        var date = a.getDate();
        var hour = a.getHours();
        var min = a.getMinutes();
        var sec = a.getSeconds();
        var time = date + ' ' + month + ' ' + year + ' ' + hour + ':' + min + ':' + sec ;
        return time;
    }
</script>