<?php
use yii\web\View;
$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;">New account: </h3>
<div class="row" style="margin: 21px;">

<div class="col-md-6">



<h3></h3>

<form method="POST">
	<p>Account type: <select name="type" class="account_type">
            <?php foreach ($account_types as $ap){ ?>
                <option value="<?= $ap['id']?>"><?= $ap['name']?></option>
            <?php } ?>
        </select></p>
    <div class="aditional_fields"></div>
	<p>Account label: <input name="label"></p>
	<p>Account name: <input name="name"></p>
	<p>Account password: <input name="password"></p>
	<input type="submit" value="add" name="add">

</form>
	</div>
</div>
<script>
    var account_types=<?=json_encode($account_types) ?>
</script>
<script>
    $( document ).ready(function() {
        aditionalFileds();
        $('.account_type').change(function () {
            aditionalFileds();
        })
    });
    function aditionalFileds() {
        $('.aditional_fields').html('')
        var account_type=$('.account_type').val()
        $.each(account_types, function( index, value ) {
            //если выбран именно этот тип аккаунта
            if(value.id==account_type){
                // создать доп поля
                $.each(value.json_fields, function( json_index, json_value ) {
                    $('.aditional_fields').append("<p>"+json_value+": <input name=\"data["+json_value+"]\"></p>")
                });
            }
        });

    }
</script>