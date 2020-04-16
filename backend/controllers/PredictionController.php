<?php
namespace backend\controllers;


use Yii;
use yii\web\Controller;

/**
 * Site controller
 */
class PredictionController extends Controller
{
	public function actionIndex()
    {
        return $this->render('index');
    }
	
	public function actionIndexPartial()
    {
        return $this->renderPartial('_index');
    }
}