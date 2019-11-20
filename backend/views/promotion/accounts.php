


	
<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;"><?=$promotion->name;?> <?=$promotion->main_currency->symbol;?> / <?=$promotion->second_currency->symbol;?></h3>


<form method="POST" style="width:724px;padding-top:0.5px;" class="list">

<h4>Select accounts:</h4>


<? foreach($accounts as $a):?>
<div>
<input type="hidden" name="account[<?=$a->id;?>]" value=0>
<input type="checkbox" <? if(in_array($a->id, $p_a)) { echo "checked";}?> name="account[<?=$a->id;?>]" value=1> <?=$a->id;?> <?=$a->label;?> <?=$a->name;?> 

</div>
<? endforeach;?>

</div>


<input type="submit" value="save" name="save">

<a href="/promotion/<?=$promotion->id;?>">Back to promotion</a>
</form>