<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
	<style>
		body{
			 background:#050505;
		}
		a,a:hover {
			text-decoration:none;
		}
		#background{
			background:url("/images/1.jpg");
			height:100%;
			width:100%;
			position:absolute;
			z-index:-1;
			filter:brightness(0.65);
		}
		#background2 {
			background: linear-gradient(-28deg, #161616, #2840387f, #161616);
			height:100%;
			width:100%;
			position:absolute;
			z-index:-1;
		}
		* {
				 font-family:Arial;
				 color:#f9f9f9;
				 margin:0;
				 padding:0;
		}
		.container {
			    width: 608px;
				position:relative;
				top:60px;
    margin: 0 auto;
    background: rgba(27, 27, 27, 0.77);
	
	box-shadow: 7px 5px 12px 3px #00000055;
			padding:0;
		}
		p {
			margin:0;
		}
		h3 {
			margin:0;
		}
		.menu {
			float:left;
			width:180px;
			padding-top:60px;
			height:677px;
			padding-left:5px;
			padding-right:5px;
			background:#00000013;
			box-shadow: inset -3px -3px 1.5px 0px #0000000d;
			
		}
		.menu a {
			color:#f9f9f9;
			display:block;
			font-size:15px;
			padding:8px 20px 8px 20px;
			width:170px;
			    background: #00000025;
				margin-bottom:1px;
				margin-left:0px;
				transition-duration:0.2s;
				cursor:pointer;
		}
		.menu a:hover {
			background: #27272730;
		}
		.trade-blocks {
			padding:8px 20px;
			width:calc(100% - 220px);
			float:left;
		}
		.trade-blocks>div {
			width:222px;
			height:222px;
			background:#00000023;
			display:inline-block;
			margin: 4px;
			transition-duration:0.2s;
			cursor:pointer;
			position:relative;
			box-shadow: 3px 2px 3px 1px #0000001a;
		}
		.trade-blocks>div:hover {
			background: #00000033;
		}
		.trade-blocks>div img.market-logo,.trade-blocks>div>svg {
			height:100px;
			display: block;
			filter: grayscale(0.6);
			
			    margin: 36px auto;
		}
		.trade-blocks>div:hover img.market-logo,.trade-blocks>div:hover>svg {
		filter: grayscale(0);
		}
		.trade-blocks>div table {
			width:calc(100% - 40px);
			margin:-8px 20px 0;
		}
		.trade-blocks>div table tr td{
			text-align:center;
			font-size:14px;
			line-height:20px;
		}
		.trade-blocks>div .status{
			    width: 100%;
			height: 4px;
			position: absolute;
			right: 0;
			top: 0;
			background:#323232bf;
			box-shadow: 1px 1px 1px 0px #0000001a;
		}
		.trade-blocks .status.green {
			background:#00a300;
		}
		#servers-status  {
			margin-top:200px;
		}
		#servers-status h4 {
			font-size:14px;
			padding: 7px 20px;
			
			color:#eeeeee;
		}
		#servers-status p {
		
			color:#eeeeee;
			padding: 3px 20px;
			font-size:12px;
		}
		#servers-status b {
			color:#00c800;
		}
		.content>.list {
			margin:5px;
			margin: 13px;
		}
		.content>.list>div {
			padding: 7px 17px;
			margin: 4px 4px 8px;
			background: rgba(0,0,0,0.2);
		}
		.content>.list>div>i {
			width:14px;
			margin-right:6px;
		}
		
		.content>.list>div .currencies {
			display:inline-block;
			width:80px;
			font-size:12px;
			color:#00c800;
		}
		
		input,select,textarea,.form-control {
			background: rgba(0,0,0,0.1);
			border:0;
			padding:5px;
			margin:2px;
			border-radius:0;
		}
		option {
			background:rgba(0,0,0,0.8);
		}
		input[type="submit"],button {
			padding:8px 20px;
		}
label.control-label {
	text-align:left;
	width: 100%;
	padding:0 5px;
}

.has-error .help-block, .has-error .control-label, .has-error .radio, .has-error .checkbox, .has-error .radio-inline, .has-error .checkbox-inline, .has-error.radio label, .has-error.checkbox label, .has-error.radio-inline label, .has-error.checkbox-inline label {
	color:red;
}

.has-success .help-block, .has-success .control-label, .has-success .radio, .has-success .checkbox, .has-success .radio-inline, .has-success .checkbox-inline, .has-success.radio label, .has-success.checkbox label, .has-success.radio-inline label, .has-success.checkbox-inline label {
	color:#00d900;
}
.btn {
	border-radius:0;
	background: #0009;
    border: 0;
    padding: 10px 71px;
}
.help-block.help-block-error {
	position:absolute;
	text-align: center;
    width: 100%;
}
	</style>
</head>
<body>
<?php $this->beginBody() ?>
<div id="background"></div>
<div id="background2"></div>

	<div class="container">

		
        <?= $content ?>

	</div>
</body>
</html>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
