<?php
namespace console\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\Currency;
use common\components\BinanceExchange;

class DepthController extends Controller
{

	public function actionCalculate($id) {

		$currency_one = Currency::findOne(['symbol'=>'USDT']);
		$currency_two = Currency::findOne($id);
		BinanceExchange::getDepth($currency_one, $currency_two);
	}
}