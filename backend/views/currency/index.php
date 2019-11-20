
<?php

/* @var $this \yii\web\View */
/* @var $currencies array */
/* @var $currency_new_id integer */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<? foreach($currencies as $currency)
		echo $this->render("_currency", ['currency' => $currency]);
?>
<a href="<?= Url::toRoute(['currency/view', 'id' => $currency_new_id]) ?>"><div class="col-md-4 currency-panel">
        <div class="panel panel-default">
            <div class="panel-body btn-primary"><p class="text-center currency-text">Create New</p></div>
        </div>
    </div>
</a>

<style>
    .currency-text{
        color: #0e0e0e;
    }
</style>
