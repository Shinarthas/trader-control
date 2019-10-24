<div style="width:733px; position:relative;" >

<a style="position:absolute;right:0;top:12px;" href="/account/add"><input type="submit" value="+" style="margin:0;"></a>
<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;">Accounts: </h3>


</div>

<div style="width:724px;padding-top:0.5px;" class="list">
<? foreach($accounts as $a): ?>
	<?=$this->render("_account", ['account'=>$a]);?>
<? endforeach; ?>

</div>
