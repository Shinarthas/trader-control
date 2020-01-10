<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */

/* @var $model \common\models\LoginForm */

/* @var $company \common\models\Company */
/* @var $coins \phpDocumentor\Reflection\Types\Object_ */
/* @var $markets \phpDocumentor\Reflection\Types\Object_ */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\JqueryAsset;
use yii\web\View;

$this->title = 'Coin Market Cap';
$this->params['breadcrumbs'][] = $this->title;

$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::POS_HEAD);


?>


<div class="row">
    <div class="col-md-6">
        <?php foreach ($coins as $coin ){?>
            <?php
            $image='/images/coins/'.$coin->cmc_id.'.png';
            ?>
            <div class="col-md-12 "><img src="<?= $image ?>"> <?= $coin->symbol ?> <?= $coin->created_at ?></div>
        <?php } ?>
    </div>
    <div class="col-md-6">
        <?php foreach ($markets as $market ){?>
            <?php
            $image='/images/markets/'.$market->cmc_id.'.png';
            ?>
            <div class="col-md-12 "><img src="<?= $image ?>"> <?= $market->name ?> <?= $market->created_at ?></div>
        <?php } ?>
    </div>

</div>