<?
	use common\components\ApiRequest;
?>

<div>
<a href="/account/<?=$account->id;?>">
<?=$account->id;?> <?=$account->label;?>
<?
	$data = ApiRequest::statistics('v1/account/get-balance', ['id'=>$account->id]);
	foreach($data->data->balances as $balance)
		echo '<span style="padding:0 20px;">'.$balance->name.' '.number_format($balance->value,2).' / ('.number_format($balance->value_in_orders,2).') </span>';
	echo '<span class="danger">USD: '.number_format($data->data->in_usd,2).'</span>'
?>
</a>
</div>