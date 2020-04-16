<?
namespace common\components;

//use common\models\CurrencyGraph;

class Diviner {

const STEPS_COUNT = 10;
// точность прогноза?
// размер выигрыша

// total ask / total bid = 1.6 сила 30%
// 
// сила стаканов
// 0 100%
// 1 75%
// 2 60%
// 3 35%
// 4 30%
// 5 25%
// 6 20%
// 7 15%
// 8 10%

	public static $power_on_lvl = [
		100,
		80,
		70,
		55,
		30,
		25,
		20,
		15,
		10
	];
	
	public static $power_on_sum = 30;

		public static function depthPrediction($asks, $bids, $pair, $currency_id) {

		$lowest_ask = 99999;
		$highest_bid = 0;
		
		
		$highest_ask = 0;
		$lowest_bid = 999999;
		
		foreach($asks as $price=>$v) {
			if($price < $lowest_ask)
				$lowest_ask = $price;
				
			$asks_prices[] = $price;
		}
		
		foreach($bids as $price=>$v) {
			if($price > $highest_bid)
				$highest_bid = $price;
				
			$bids_prices[] = $price;
		}
		
		foreach($asks as $price=>$v) {
			if($price > $highest_ask && $price < $lowest_ask*1.006)
				$highest_ask = $price;
		}
		
		foreach($bids as $price=>$v) {
			if($price < $lowest_bid && $price > $highest_bid*0.994)
				$lowest_bid = $price;
		}
		
		
		$depth_space =  ($lowest_ask/$highest_bid - 1)*100;
		if($depth_space > 0.08) {
			return ['prediction'=> 0];
		}
		
		
		$ask_width = $highest_ask - $lowest_ask;
		$bid_width = $highest_bid - $lowest_bid;
		
		if($ask_width<$bid_width) {
			$depth_step = $ask_width/self::STEPS_COUNT;
			$depth_step_percent = ( $depth_step / $asks_prices[0] ) * 100;
		}
		else {
			$depth_step = $bid_width/self::STEPS_COUNT;
			$depth_step_percent = ( $depth_step / $bids_prices[0] ) * 100;
		}
		
		$step_coefficient = 0.01 / $depth_step_percent;
		
		
		for($i=0; $i<=self::STEPS_COUNT; $i++) {
			$asks_limits[$i] = $lowest_ask+($i*$depth_step);
			$bids_limits[$i] = $highest_bid-($i*$depth_step);
			
			if($i==0) {
				$asks_limits[$i]--;
				$bids_limits[$i]++;
			}
		}
		
		$asks_depth_graph = [];
		$bids_depth_graph = [];
		
		foreach($asks as $price=>$v) {
			for($i=0; $i<self::STEPS_COUNT; $i++) {
				if($price > $asks_limits[$i] AND $price<= $asks_limits[$i+1]) {
					$asks_depth_graph[$i]+= $v;
					break;
				}
			}
		}
		
		$sum_bids = 0;
		
		foreach($bids as $price=>$v) {
			$sum_bids+=$v;
			
			for($i=0; $i<self::STEPS_COUNT; $i++) {
				if($price < $bids_limits[$i] AND $price>= $bids_limits[$i+1]) {
					$bids_depth_graph[$i]+= $v;
					break;
				}
			}
		}
		
		$bid_width_percent = ($bid_width / $bids_prices[0])*100;
		$global_avg_depth_volume = self::getAndCalcAvgDepthVolume($sum_bids, $bid_width_percent, $pair);
	$asks_limits[0]++;
		$bids_limits[0]--;
		
		$total_chance = 0;
		$total_chance_steps = 0;
		
		$statistic = [];
			
			$cache = \Yii::$app->cache;
			$cache->set("wtf", $asks_depth_graph[0] * $step_coefficient * 100);
			$cache->set("wtf1", $bids_depth_graph[0] * $step_coefficient * 100);
			$cache->set("wtf2", $global_avg_depth_volume);
			
			$cache->set("wttt", $asks_depth_graph[0] );
			$cache->set("wtt", $step_coefficient);
		
		for($i=0;$i<self::STEPS_COUNT; $i++) {
			if($asks_depth_graph[$i]==0){
				continue;
			}
			
			$temp_chance = ($bids_depth_graph[$i] / $asks_depth_graph[$i]);
			
			if($temp_chance>5)
				$temp_chance = 5;
				
			
		 /*	if( $temp_chance > 1 ) {
				if($global_avg_depth_volume > $asks_depth_graph[$i] * $step_coefficient) {
					$temp_chance = ($bids_depth_graph[$i] / ( ($asks_depth_graph[$i] + ($global_avg_depth_volume/$step_coefficient) )/2 ));
				}
					
				if($global_avg_depth_volume > $bids_depth_graph[$i] * $step_coefficient) {
					$temp_coefficient = ($bids_depth_graph[$i] * $step_coefficient) / $global_avg_depth_volume;
					$temp_chance*= $temp_coefficient;
					if($temp_chance < 1)
						$temp_chance = 1;
				}
			}
		*/
			if($temp_chance<1)
				$chance_on_lvl = round(30+(20*$temp_chance), 2);
			else
				$chance_on_lvl = round(45+(5*$temp_chance), 2);
			
			if($chance_on_lvl>70)
				$chance_on_lvl = 70;
			
			$statistic[$i] = ['power' => $bids_depth_graph[$i] * $step_coefficient , 'avg_power' => $global_avg_depth_volume, 'chance_on_lvl' => $chance_on_lvl];
			
			$total_chance+= $chance_on_lvl*self::$power_on_lvl[$i];
			$total_chance_steps+= self::$power_on_lvl[$i];
		}
		
		if($total_chance_steps == 0)
			return ['prediction'=> 1];
			
		$prediction = $total_chance/$total_chance_steps;
		
			
		$cache = \Yii::$app->cache;
		$cache->set("statistic_".$pair, ['statistic'=>$statistic, 'prediction'=>$prediction,'asks_depth_graph'=>$asks_depth_graph, 'bids_depth_graph'=>$bids_depth_graph, 'asks_limits'=>$asks_limits, 'bids_limits'=>$bids_limits]);

/*
		$g = new CurrencyGraph;
		$g->prediction = $prediction;
		$g->price = ($highest_bid + $lowest_ask) / 2;
		$g->currency_id = $currency_id;
		$g->created_at = time();
		$g->save();
	*/	
		return ['prediction'=> $prediction, 'ask_width'=> $ask_width/$asks_prices[0], 'lowest_ask'=>$lowest_ask];
		//return ['prediction'=>$prediction,'asks_depth_graph'=>$asks_depth_graph, 'bids_depth_graph'=>$bids_depth_graph, 'asks_limits'=>$asks_limits, 'bids_limits'=>$bids_limits];
		
		echo "<pre>";
		print_r($asks_depth_graph);
		echo "</pre><pre>";
		print_r($bids_depth_graph);
		echo "</pre><pre>";
		print_r($asks_limits);
		echo "</pre><pre>";
		print_r($bids_limits);
		echo "</pre>";
		echo 'ask width: '.$ask_width.'%<br>';
		echo 'bid width: '.$bid_width.'%';
	}
	
	public static function depthPrediction2($asks, $bids, $pair) {

		foreach($asks as $price=>$v)
			$asks_prices[] = $price;
		
		foreach($bids as $price=>$v)
			$bids_prices[] = $price;
		
		$lowest_ask = $asks_prices[0];
		$highest_bid = $bids_prices[0];
		
		$depth_space =  ($lowest_ask/$highest_bid - 1)*100;
		echo '<h3>space: '.$depth_space.'</h3>';
		
		$ask_width = ($asks_prices[count($asks_prices)-1] - $asks_prices[0]);
		$bid_width = ($bids_prices[0] - $bids_prices[count($bids_prices)-1]);
		
		if($ask_width<$bid_width) {
			$depth_step = $ask_width/self::STEPS_COUNT;
			$depth_step_percent = ( $depth_step / $asks_prices[0] ) * 100;
		}
		else {
			$depth_step = $bid_width/self::STEPS_COUNT;
			$depth_step_percent = ( $depth_step / $bids_prices[0] ) * 100;
		}
		
		$step_coefficient = 0.01 / $depth_step_percent;
		
		
		for($i=0; $i<=self::STEPS_COUNT; $i++) {
			$asks_limits[$i] = $lowest_ask+($i*$depth_step);
			$bids_limits[$i] = $highest_bid-($i*$depth_step);
			
			if($i==0) {
				$asks_limits[$i]--;
				$bids_limits[$i]++;
			}
		}
		
		$asks_depth_graph = [];
		$bids_depth_graph = [];
		
		foreach($asks as $price=>$v) {
			for($i=0; $i<self::STEPS_COUNT; $i++) {
				if($price > $asks_limits[$i] AND $price<= $asks_limits[$i+1]) {
					$asks_depth_graph[$i]+= $v;
					break;
				}
			}
		}
		
		$sum_bids = 0;
		
		foreach($bids as $price=>$v) {
			$sum_bids+=$v;
			
			for($i=0; $i<self::STEPS_COUNT; $i++) {
				if($price < $bids_limits[$i] AND $price>= $bids_limits[$i+1]) {
					$bids_depth_graph[$i]+= $v;
					break;
				}
			}
		}
		
		$bid_width_percent = ($bid_width / $bids_prices[0])*100;
		$global_avg_depth_volume = self::getAndCalcAvgDepthVolume($sum_bids, $bid_width_percent, $pair);
		echo '<h1>avg: '.$global_avg_depth_volume.'</h1>';
		
		$asks_limits[0]++;
		$bids_limits[0]--;
		
		$total_chance = 0;
		$total_chance_steps = 0;
		
		for($i=0;$i<self::STEPS_COUNT; $i++) {
			if($asks_depth_graph[$i]==0){
				echo '60<br>';
				continue;
			}
			
			$temp_chance = ($bids_depth_graph[$i] / $asks_depth_graph[$i]);
			
			if($temp_chance>5)
				$temp_chance = 5;
				
			if( $temp_chance > 1 ) {
				if($global_avg_depth_volume > $asks_depth_graph[$i] * $step_coefficient) {
					$temp_chance = ($bids_depth_graph[$i] / ( ($asks_depth_graph[$i] + ($global_avg_depth_volume/$step_coefficient) )/2 ));
				}
					
				if($global_avg_depth_volume > $bids_depth_graph[$i] * $step_coefficient) {
					$temp_coefficient = ($bids_depth_graph[$i] * $step_coefficient) / $global_avg_depth_volume;
					$temp_chance*= $temp_coefficient;
					if($temp_chance < 1)
						$temp_chance = 1;
				}
			}
		
			if($temp_chance<1)
				$chance_on_lvl = round(30+(20*$temp_chance), 2);
			else
				$chance_on_lvl = round(45+(5*$temp_chance), 2);
			
			if($chance_on_lvl>70)
				$chance_on_lvl = 70;
			
			echo $i.' c: '.$bids_depth_graph[$i] * $step_coefficient.' g: '.$global_avg_depth_volume.' c: '.$chance_on_lvl.'%<br>';
			
			$total_chance+= $chance_on_lvl*self::$power_on_lvl[$i];
			$total_chance_steps+= self::$power_on_lvl[$i];
			//echo $chance_on_lvl.'<br>';
		}
		
		$prediction = $total_chance/$total_chance_steps;
		
		echo "<h1>PREDICTION: ".$prediction."</h1>";
		
		return ['prediction'=>$prediction,'asks_depth_graph'=>$asks_depth_graph, 'bids_depth_graph'=>$bids_depth_graph, 'asks_limits'=>$asks_limits, 'bids_limits'=>$bids_limits];
		
		echo "<pre>";
		print_r($asks_depth_graph);
		echo "</pre><pre>";
		print_r($bids_depth_graph);
		echo "</pre><pre>";
		print_r($asks_limits);
		echo "</pre><pre>";
		print_r($bids_limits);
		echo "</pre>";
		echo 'ask width: '.$ask_width.'%<br>';
		echo 'bid width: '.$bid_width.'%';
	}

	//
	// В этой функции я расчитываю среднюю стоимость ордеров в стаканах
	// Я перевожу волюм ордеров в диапазоне 0.01% стоимости, поскольку длина стаканов приходит разная
	// Это необходимо чтобы понимать, насколько большие сейчас стаканы
	//
	public function getAndCalcAvgDepthVolume($sum_volume, $depth_percent, $pair) {
//	echo '<h3>percent '.$depth_percent.'</h3>';
//	echo '<h3>sum volume '.$sum_volume.'</h3>';
		$cache = \Yii::$app->cache;
		
		$current_calculated_volume = $sum_volume * (0.01 / $depth_percent);
	
//	echo '<h3>current calculated volume '.$current_calculated_volume.'</h3>';
	
//	echo '<h4>'."avg_depth_volume_".$pair.'</h4>';
		$cache_volume = $cache->get("avg_depth_volume_".$pair);
//$cache_volume = 0;
		if($cache_volume == 0){
			$cache_volume = $current_calculated_volume;
			$cache->set("avg_depth_volume_".$pair, $cache_volume);
			
			return $cache_volume;
		}
		
		$cache_volume+= ($current_calculated_volume-$cache_volume)/100;
		$cache->set("avg_depth_volume_".$pair, $cache_volume);
		
		return $cache_volume;
	}
	
	public function getAvgDepthVolume($pair) {
		return \Yii::$app->cache->get("avg_depth_volume_".$pair);
	}
}

?>

