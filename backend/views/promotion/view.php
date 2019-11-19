<?
	use common\models\AccountBalance;
	use common\models\CurrencyPrice;
	use common\models\Currency;
	
	$currencies = Currency::find()->all();
?>

<style>

		table tr>* {
			padding: 5px;
			border-bottom: 1px solid #888;
		}
</style>


	
<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;"><?=$promotion->name;?> <?=$promotion->main_currency->symbol;?> / <?=$promotion->second_currency->symbol;?></h3>
<div class="row" style="margin: 21px;">

<div class="col-md-6">

<?
$speed_timeout = 3600;
if($promotion->mode == $promotion::MODE_FAST_EARN)
	$speed_timeout = 1200;

?>

<font>Accounts: <?=count($promotion->accounts);?></font> <a href="/promotion/<?=$promotion->id;?>/accounts">Edit</a> <a href="/market/<?=$promotion->market->id;?>">Back to market</a>


	<br>

	<br>


<?=$promotion->enabled==1?'enabled':'disabled';?>

<h3></h3>

<form method="POST">
	<p>Name: <input name="Promotion[name]" value="<?=$promotion->name;?>"></p>
	<p>Hour volume: <input name="settings[hour_volume]" value="<?=$promotion->settings['hour_volume'];?>"> <?=$promotion->main_currency->symbol;?></p>

	<p>mode: <select  name="Promotion[mode]"><? foreach($promotion::$modes as $value=>$mode) { $selected = ""; if($value == $promotion->mode) {$selected = "selected";}  echo "<option value='".$value."' ".$selected.">".$mode."</option>";}?></select></p>

    <p>currency one: <select  name="Promotion[currency_one]"><? foreach($currencies as $c) { $selected = ""; if($promotion->currency_one == $c->id) {$selected = "selected";}  echo "<option value='".$c->id."' ".$selected.">".$c->symbol."</option>";}?></select></p>

    <p>currency two: <select  name="Promotion[currency_two]"><? foreach($currencies as $c) { $selected = ""; if($promotion->currency_two == $c->id) {$selected = "selected";}  echo "<option value='".$c->id."' ".$selected.">".$c->symbol."</option>";}?></select></p>

	<p>exchanges per hour: <select name="settings[frequency]"><? foreach($promotion::$frequency_variants as $v=>$f) {  $selected = ""; if($v == $promotion->settings['frequency']) {$selected = "selected";}  echo "<option value=".$v." ".$selected.">".$f."</option>";}?></select></p>
	<p>Make them per day tasks <input type="checkbox" name="settings[day_tasks]" <?=$promotion->settings['day_tasks']==1?"checked":"";?> value="1"></p>
		
	<p>increase/decrease per day<input name="settings[speed]" value="<?=$promotion->settings['speed'];?>">%</p>
	
	<p>stabilize price treshold <input name="settings[price_threshold]" value="<?=$promotion->settings['price_threshold'];?>"> <?=$promotion->main_currency->symbol;?></p>
	
	<p>stabilize price power <input name="settings[price_stabilize_power]" value="<?=$promotion->settings['price_stabilize_power'];?>" style="width:40px;"> %</p>
	
	<p>Extreme order cancel <input name="settings[order_cancel]" value="<?=$promotion->settings['order_cancel'];?>" style="width:40px;"></p>
	<p>earn percent <input name="settings[earn_percent]" value="<?=$promotion->settings['earn_percent'];?>" style="width:40px;"> %</p>
	
	
	<input type="hidden"  name="settings[disable_balance_check]" value="0">
	<p>disable balance check <input type="checkbox" name="settings[disable_balance_check]" <?=$promotion->settings['disable_balance_check']==1?"checked":"";?> value="1"></p>
	<input type="hidden"  name="settings[calculate_account]" value="0">
		<p>fixed tasks: <br>
		<input name="settings[fixed_tasks_currency_one]" value="<?=(int)$promotion->settings['fixed_tasks_currency_one'];?>"><?=$promotion->main_currency->symbol;?><br>
		<input name="settings[fixed_tasks_currency_two]" value="<?=(int)$promotion->settings['fixed_tasks_currency_two'];?>"><?=$promotion->second_currency->symbol;?>
	</p>
	
	<hr>
	<p>calculate account <input type="checkbox" name="settings[calculate_account]" <?=$promotion->settings['calculate_account']==1?"checked":"";?> value="1"></p>
	<p>limit per account: <input name="settings[limit_per_account]" value="<?=(int)$promotion->settings['limit_per_account'];?>"></p>
	<input type="hidden"  name="settings[only_day]" value="0">
	<p>only day actions <input type="checkbox" name="settings[only_day]" <?=$promotion->settings['only_day']==1?"checked":"";?> value="1"></p>

	
	<input type="submit" value="Save" name="save">
</form>
	
	<form method="POST">
		<?=$promotion->enabled==1?'<input type="submit" value="stop" style="width:180px;" name="stop">':'<input type="submit" value="start" style="width:180px;" name="start">';?>
	</form>
	
</div>

<div class="col-md-6">
	<h3>History:</h3>
	<table>
	<thead>
		<tr>
			<th>direction</th>
			<th>acc</th>
			<th>date</th>
			<th><?=$promotion->second_currency->symbol;?></th>
			<th>rate</th>
			<th><?=$promotion->main_currency->symbol;?></th>
			<th>status</th>
		</tr>
	</thead>
	<tbody>
	<? foreach($promotion->tasks as $t): ?>
		<tr>
			<td><?=($t->sell==1)?'<b style="color:orange">sell</b>':'<b style="color:purple;">buy</b>';?></td>
			<td><?=$t->account_id;?></td>
			<td><?=date("d/m/y H:i", $t->time);?></td>
			<td><?=$t->tokens_count;?></td>
			<td><?=$t->rate;?></td>
			<td><?=$t->rate*$t->tokens_count;?></td>
			<td><? if($t->status==1){echo "<b style='color:red'>error</b>";} else if($t->status==2){echo "OK";
				if($t->progress != 100) {
					echo '<b style="color:red"> ('.$t->progress.'%)</b>';
				}
			}else if($t->status==3){
				echo "price error";
			} else {
				echo $t::$statuses[$t->status];
			}

			?></td>
			
		</tr>
	
	<? endforeach; ?>
	<tbody>
	</table>
	
</div>

</div>