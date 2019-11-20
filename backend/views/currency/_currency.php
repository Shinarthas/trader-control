<?php

/* @var $this \yii\web\View */
/* @var $currency \common\models\Currency */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;
use yii\helpers\Url;
AppAsset::register($this);
?>
<a href="<?= Url::toRoute(['currency/view', 'id' => $currency->id]) ?>"><div class="col-md-4 currency-panel">
        <div class="panel panel-default">
            <div class="panel-body"><p class="text-center currency-text"><?= $currency->symbol;?></p></div>
        </div>
    </div>
</a>