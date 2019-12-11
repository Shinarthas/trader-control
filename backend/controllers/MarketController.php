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
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    public function actionView($id)
    {
		$market = Market::find()->where(['id'=>$id])->with('promotions')->one();;
		
        return $this->render('view', ['market'=>$market]);
    }
	
	public function actionCampaign() {
		return $this->render("campaign");
	}
}
