<?
use common\components\BinanceExchange;
use common\models\Debth;


use common\models\Currency;
$cache = \Yii::$app->cache;
/*
echo "<pre>";
print_r($cache->get("statistic_BTCUSDT") );
echo "</pre>";
*/


$currency_one = Currency::findOne(['symbol'=>'USDT']);
?>



<style>
.depth_graph {
	position:relative;
	height:200px;
	width:560px;
	display:inline-block;
}
.depth_graph>div {
	position:absolute;
	width:22px;
	bottom:30px;
}
.depth_graph>div.asks {
	background:#bf15ff85;
	z-index:1;
}
.depth_graph>div.bids {
	background:#00f20099;
	z-index:1;
}
</style>


<?
foreach(Currency::find()->all() as $currency_two) {
if($currency_two->symbol == 'USDT')
	continue;


$data = $cache->get("statistic_".$currency_two->symbol."USDT");
?>
<h1>
<?=$cache->get("wtf");?>
</h1>
<h1>
<?=$cache->get("wtf1");?>
</h1>
<h1>
<?=$cache->get("wtf2");?>
</h1>
<h1>
<?=$cache->get("wttt");?>
</h1>
<h1>
<?=$cache->get("wtt");?>
</h1>
<? 
	$max_graph_volume = 0;
	$sum_asks_graph = 0;
	$sum_bids_graph = 0;
	
	foreach($data['asks_depth_graph'] as $v) {
		if($v>$max_graph_volume)
			$max_graph_volume = $v;
			
		$sum_asks_graph+=$v;
	}
	
	foreach($data['bids_depth_graph'] as $v) {
		if($v>$max_graph_volume)
			$max_graph_volume = $v;
		
		$sum_bids_graph+=$v;
	}
	
	if($sum_asks_graph > $sum_bids_graph)
		$max_sum_graph = $sum_asks_graph;
	else
		$max_sum_graph = $sum_bids_graph;
		
	$sum_asks_graph = 0;
	$sum_bids_graph = 0;
?>
<div style="border-bottom:2px solid #eee;padding: 40px 20px;">

<div style="width:520px;height:90px;">
<h3 style="font-size:48px;float:left;"><?=$currency_two->symbol;?></h3>
<h3 style="font-size:40px;float: right;"><?= round($data['prediction'],2);?>%</h3>
</div>
<div class="depth_graph">
<? 
	for($k=0;$k<10;$k++) {
		$v = $data['bids_depth_graph'][$k];
		$sum_bids_graph+=$v;
		
		echo "<div  class='bids' style='left:". ((10-$k)*25) ."px;height:". (int)(($v/$max_graph_volume)*100) ."px;'></div>";
	
		echo "<div style='background:#8888886a; left:". ((10-$k)*25) ."px; height:". ($sum_bids_graph/$max_sum_graph)*160 ."px;'></div>";
	}
?>

<? 
	for($k=0;$k<10;$k++) {
		$v = $data['asks_depth_graph'][$k];
		
		$sum_asks_graph+=$v;
	
		echo "<div class='asks' style='left:". (($k+11)*25) ."px;height:". (int)(($v/$max_graph_volume)*100) ."px;'></div>";
		
		echo "<div style='background:#8888886a; left:". (($k+11)*25) ."px; height:". ($sum_asks_graph/$max_sum_graph)*160 ."px;'></div>";
	}
?>

	<p style="position:absolute; bottom:0;left:0;"><?=$data['bids_limits'][9];?></p>
	<p style="position:absolute; bottom:0;left:<?=((20*25)-40);?>px;"><?=$data['asks_limits'][9];?></p>
	<p style="position:absolute; top:0;left:0;"><?=round($max_sum_graph,2);?></p>
</div>

<div style="display:inline-block;vertical-align: top;">
	<div style="line-height:1.3;">
	<?
		for($k=0;$k<10;$k++) {
			$v = $data['statistic'][$k];
			echo '<small>'.$v['chance_on_lvl'].' '.round($v['power'],5).' '.round($v['avg_power'],5).'</small><br>';
		}
	?>
	</div>
</div>

</div>
<?
}
?>