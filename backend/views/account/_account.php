<?
	use common\components\ApiRequest;
?>

<div>
<a href="/account/<?=$account->id;?>">
<?=$account->id;?> <?=$account->label;?>
<?
	$data = ApiRequest::statistics('v1/account/get-balance', ['id'=>$account->id]);
	foreach($data->data->balances as $balance)
		echo '<span style="padding:0 20px;">'.$balance->name.' '.$balance->value.' / ('.$balance->value_in_orders.') </span>';
	echo '<span class="danger">USD: '.$data->data->in_usd.'</span>'
?>
</a>
</div>