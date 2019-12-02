<?
	use common\components\ApiRequest;
?>
<style>
table tr>th {
	padding:4px 10px;
	text-align:center;
}
table tr>* {
	border:1px solid #555;
	padding:4px;
}
</style>

 <?=$a->label;?>
<?
	$currencies = [];
	$req = ApiRequest::statistics('v1/acc-balance/index', ['account_id'=>$a->id, 'limit'=>24]);

	$data = [];
	foreach($req->data as $balance) {
		$currencies[$balance->name] = 1;
		$data[$balance->timestamp/120][$balance->name] = [$balance->value, $balance->value_in_orders];
	}
?>


<table>
	<tr>
		<th rowspan = 2>time</th>
		<? foreach($currencies as $currency_name => $unused): ?>
			<th colspan = 3><?=$currency_name?></th>
		<? endforeach;?>
	</tr>
	<tr>
		<? foreach($currencies as $currency_name => $unused): ?>
			<th>balance</th>
			<th>orders</th>
			<th>total</th>
		<? endforeach;?>
	</tr>
	<? foreach($data as $time=>$balance): ?>
		<tr>
			<td><?=date("d H:i",$time*120)?></td>
			<? foreach($currencies as $currency_name => $unused): ?>
				<td><?=$balance[$currency_name][0]?></td>
				<td><?=$balance[$currency_name][1]?></td>
				<td><?=$balance[$currency_name][0]+$balance[$currency_name][1];?></td>
			<? endforeach; ?>
		</tr>
	<? endforeach;?>
</table>