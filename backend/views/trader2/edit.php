<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginForm */

/* @var $company \common\models\Company */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = 'Trader 2';
$this->params['breadcrumbs'][] = $this->title;

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

<form>
    <div class="row">
        <div class="col-md-6">
            <input type="hidden" name='id' value="<?php echo isset($company) ? $company->id : '' ?>">
            <div class="row"><div class="col-md-2">name:</div>
                <div class="col-xs-4"><input class="form-control" name='name'
                                             value="<?php echo isset($company) ? $company->name : 'no name' ?>"></div></div>
            <div class="row"><div class="col-md-2">Entrance currency:</div>
                <div class="col-xs-4"><input class="form-control" name='entrance_currency'
                                             value="<?php echo isset($company) ? $company->entrance_currency : 'USDT' ?>"></div></div>
            <div class="row"><div id="settings" style="width: 400px; height: 400px;"></div>
            </div>
            <div class="row"><div class="col-md-2">Trigger Score:</div>
                <div class="col-xs-4"><input class="form-control" name='trigger_score'
                                             value="<?php echo isset($company) ? $company->trigger_score : 100 ?>"></div></div>
            <div class="row">
                <div class="col-md-2">Maximal Stake:</div>
                <div class="col-xs-4"><input class="form-control" name='maximal_stake'
                                             value="<?php echo isset($company) ? $company->maximal_stake : 20 ?>"></div></div>
            <div class="row">  <div id="strategy" style="width: 400px; height: 400px;"></div>

                <div class="col-md-2">Timeout:</div>
                <div class="col-xs-4"><input class="form-control" name='timeout'
                                             value="<?php echo isset($company) ? $company->timeout : 3600 * 24 ?>"></div></div>
            <div class="row">    <div id="accounts" style="width: 400px; height: 400px;"></div></div>
        </div>
        <button type="button" class="btn btn-primary save-button">Save</button>


    </div>
</form>

<script>

    var settings=<?= $company?json_encode($company->settings):json_encode([
            [
                    'parameter'=>'24h_volume_percent',
                    'limit'=>'20',
                    'sign'=>'>',
                    'value'=>5,
            ],
        [
            'parameter'=>'exchange_rate_percent',
            'limit'=>'3',
            'sign'=>'>',
            'value'=>5,
        ]]) ?>;
    var settings_editor = new JSONEditor($('#settings')[0])
    settings_editor.set(settings)

    var strategy=<?= $company?json_encode($company->strategy):
        json_encode([
            'stop_loss'=>2,
            'take_profit'=>7,
            'reset_position_on_drop'=>1,
            'action'=>'buy'
        ])?>;
    var strategy_editor = new JSONEditor($('#strategy')[0])
    strategy_editor.set(strategy)

    var accounts=<?=  $company?json_encode($company->accounts):json_encode([17,18,27,28])?>;
    var accounts_editor = new JSONEditor($('#accounts')[0])
    accounts_editor.set(accounts)


    $( document ).ready(function() {
        $('.save-button').click(function () {
            console.log(this)
            var form=$(this).parents('form');
            var data={
                'id' : form.find('input[name=\'id\']').val(),
                'name' : form.find('input[name=\'name\']').val(),
                'entrance_currency' : form.find('input[name=\'entrance_currency\']').val(),
                'trigger_score' : form.find('input[name=\'trigger_score\']').val(),
                'maximal_stake' : form.find('input[name=\'maximal_stake\']').val(),
                'timeout' : form.find('input[name=\'timeout\']').val(),

                'settings': settings_editor.get(),
                'strategy': strategy_editor.get(),
                'accounts': accounts_editor.get()
            };

            //csrf protection
            var param = $('meta[name=csrf-param]').attr("content");
            var token = $('meta[name=csrf-token]').attr("content");
            data[param]=token;
            console.log(data);
            $.ajax({
                type: "POST",
                url: form.attr('action'),
                data: data,
                success: function (msg) {
                    console.log(msg)
                },
            });
        })
    });
</script>
