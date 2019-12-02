<?
	use common\components\ApiRequest;
?>

<div>
<a href="/account/<?=$account->id;?>">
<?=$account->id;?> <?=$account->label;?>
<?
	$data = ApiRequest::statistics('v1/acc-balance/last', ['account_id'=>$account->id]);

	foreach($data->data as $balance) 
		echo '<span style="padding:0 20px;">'.$balance->name.' '.$balance->value.'/'.$balance->value_in_orders.'</span>';
	
?>
</a>
</div>