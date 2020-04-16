<div id="graph">
<?
	echo $this->render("_index");
?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>

	setInterval(function(){
		$("#graph").load("/prediction/index-partial");
	},1000)
</script>