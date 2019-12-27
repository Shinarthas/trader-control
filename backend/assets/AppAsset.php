<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
		'css/Font-Awesome-master/Font-Awesome-master/css/font-awesome.min.css'
    ];
    public $js = [
    ];
    public $depends = [
        'backend\assets\FontAwesomeAsset',
     //   'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
	
}
