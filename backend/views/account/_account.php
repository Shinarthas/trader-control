<?
	use common\components\ApiRequest;
?>

<div>
<?=$account->id;?> <?=$account->label;?>
<?
	$data = ApiRequest::statistics('v1/acc-balance/index', ['account_id'=>$account->id, 'limit'=>2]);

	foreach($data->data as $balance) 
		echo '<span style="padding:0 20px;">'.$balance->name.' '.$balance->value.'/'.$balance->value_in_orders.'</span>';
	
?>
</div>