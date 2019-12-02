	
			<div style="float:right;width:372px;padding-right:20px;">
				<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;">Statistic:</h3>
				<div style="width:100%;height:600px;background:rgba(0,0,0,0.2);margin-top:11px;">
				
				</div>
			</div>		


<a style="position:absolute;left:864px;top:12px;" href="/promotion/add?market=<?=$market->id;?>"><input type="submit" value="+" style="margin:0;"></a>
		<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;"><?=$market->name?> promotions:</h3>

			<div style="width:724px;" class="list">
				<? foreach($market->promotions as $p) :?>
					<div>
						<?=$p->enabled==1?'<i class="fa fa-play"></i>':'<i class="fa fa-stop"></i>';?>
						<span class="currencies"><?=$p->main_currency->symbol;?> / <?=$p->second_currency->symbol;?></span>
						<a href="/promotion/<?=$p->id;?>"><?=$p->name; ?> <?=$p::$modes[$p->mode]['name'];?></a>
						
						<p style="float:right; display:inline-block;width:60px;text-align:right;font-size:13px;line-height:20px;"><?=date("M d",$p->created_at);?></p>
						<? $errors = $p->errors_percent; ?>
						<p style="float:right; display:inline-block;width:60px;text-align:right;font-size:13px;line-height:20px; <? if($errors>30) echo "color:red;";?>"><?=(int)$errors;?>%</p>
						<p style="float:right; font-size:13px;line-height:20px;opacity:0.3;"><?=count($p->promotionAccounts);?></p>
					<?/*	<p style="float:right; font-size:13px;line-height:20px;opacity:0.3;">25'666$<span style="margin-left:10px;">+1.5%</span></p>*/?>
					</div>
				<? endforeach;?>
			</div>
			
