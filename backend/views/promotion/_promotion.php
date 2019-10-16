<div style="border: 1px solid #eee;border-width:1px 0 1px 0;">
	<a href="/promotion/<?=$promotion->id;?>"><h2><?=$promotion->name;?></h2></a>
	<p><b><?=$promotion->enabled==1?'enabled':'disabled';?></b></p>
	<p><a href="<?=$promotion->platform->url;?>"><?=$promotion->platform->url;?></a></p>
	<h3><?=$promotion->main_currency->symbol;?> - <?=$promotion->second_currency->symbol;?></h3>
	<p>Hour volume: <?=$promotion->hour_volume;?> <?=$promotion->main_currency->symbol;?></p>
	<p>mode: <?=$promotion->mode==1?'encrease price':'stabilize the price';?></p>
	<p>Accounts connected: <?=$promotion->countAccounts;?></p>
	<p>Balance: 100TRX, 300MLT, 500GPX</p>
	график из истории продаж
</div>