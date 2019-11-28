<?
	use common\models\AccountBalance;
	use common\models\CurrencyPrice;
	use common\models\Currency;
	use yii\web\View;
/* @var $currency \common\models\Currency */

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);
?>
<link href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/7.0.4/jsoneditor.css" rel="stylesheet" type="text/css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" rel="stylesheet" type="text/css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/7.0.4/jsoneditor.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.js"></script>
<style>

		table tr>* {
			padding: 5px;
			border-bottom: 1px solid #888;
		}
    .jsoneditor-tree{
        background: #f9f9f9;

    }
    .jsoneditor-treepath{
        color: #808080;
    }
        .jsoneditor *{
            color:#808080 !important;
        }
</style>


	
<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;"><?=$currency->symbol;?></h3>
<div class="row" style="margin: 21px;">

<div class="col-md-6">


<h3></h3>
    <!--<form action="/file-upload" class="dropzone">
        <div class="fallback">
            <input name="file" type="file" multiple />
        </div>
    </form>-->
<form method="POST">
	<p>Name: <input name="currency[name]" class="name" value="<?=$currency->name;?>"></p>
	<p>Symbol: <input name="currency[symbol]" class="symbol" value="<?=$currency->symbol;?>"></p>
	<p>Decimals: <input name="currency[decimals]" class="decimals" value="<?=$currency->decimals;?>"></p>
	<input name="currency[type]"  type="hidden"  class="type" value="<?=1;?>">
	<p>Address: <input name="currency[address]" class="address" value="<?=strlen($currency->address)>3?$currency->address:'0x';?>"></p>
	<p>Address: <input name="currency[class]" class="class" value="<?=strlen($currency->class)>3?$currency->class:'GoodCurrency';?>"></p>




    <div id="data" style="width: 400px; height: 400px;"></div>

	<button type="button" class="btn btn-primary save-button" name="save">Save</button>
</form>

	
</div>

<div class="col-md-6">
	<h3>History:</h3>
	<table>
	<thead>
		<tr>
			<th>direction</th>
			<th>acc</th>
			<th>date</th>
			<th><?=$promotion->second_currency->symbol;?></th>
			<th>rate</th>
			<th><?=$promotion->main_currency->symbol;?></th>
			<th>status</th>
		</tr>
	</thead>
	<tbody>
	<? foreach($promotion->tasks as $t): ?>
		<tr>
			<td><?=($t->sell==1)?'<b style="color:orange">sell</b>':'<b style="color:purple;">buy</b>';?></td>
			<td><?=$t->account_id;?></td>
			<td><?=date("d/m/y H:i", $t->time);?></td>
			<td><?=$t->tokens_count;?></td>
			<td><?=$t->rate;?></td>
			<td><?=$t->rate*$t->tokens_count;?></td>
			<td><? if($t->status==1){echo "<b style='color:red'>error</b>";} else if($t->status==2){echo "OK";
				if($t->progress != 100) {
					echo '<b style="color:red"> ('.$t->progress.'%)</b>';
				}
			}else if($t->status==3){
				echo "price error";
			} else {
				echo $t::$statuses[$t->status];
			}

			?></td>
			
		</tr>
	
	<? endforeach; ?>
	<tbody>
	</table>
	
</div>

</div>


<script>
    var data=<?= json_encode($currency->data)?>
    // create the editor
    const container = document.getElementById("data")
    const options = {}
    const editor = new JSONEditor(container, options)

    // set json
    const initialJson = data
    editor.set(initialJson)

    // get json
    const updatedJson = editor.get()
    $("div.image-dropzone").dropzone({ url: "/file/post" });

    $( document ).ready(function() {
        $('.save-button').click(function () {
            console.log(this)
            var form=$(this).parents('form');
            var data={
                'name' : form.find('.name').val(),
                'symbol' : form.find('.symbol').val(),
                'decimals' : form.find('.decimals').val(),
                'type' : form.find('.type').val(),
                'address' : form.find('.address').val(),
                'class' : form.find('.class').val(),
                'data': editor.get()
            };

            //csrf protection
            var param = $('meta[name=csrf-param]').attr("content");
            var token = $('meta[name=csrf-token]').attr("content");
            data[param]=token;
            console.log(data);
            $.ajax({
                type: "POST",
                //url: window.location,
                data: data,
                success: function (msg) {
                    console.log(msg)
                },
            });
        })
    });
</script>