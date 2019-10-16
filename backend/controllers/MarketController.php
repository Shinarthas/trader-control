<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use common\models\Market;

/**
 * Site controller
 */
class MarketController extends Controller
{

    public function actionView($id)
    {
		$market = Market::find()->where(['id'=>$id])->with('promotions')->one();;
		
        return $this->render('view', ['market'=>$market]);
    }
}
