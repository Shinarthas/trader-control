<?php
namespace backend\controllers;

use common\components\ApiRequest;
use Yii;
use yii\web\Controller;
use common\models\Market;

/**
 * Site controller
 */
class LogsController extends Controller
{
    static $limit=200;
	public function beforeAction($action)
	{            
		if (Yii::$app->user->isGuest) {
            return $this->redirect("/site/login");
        }
		
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    public function actionIndex()
    {
        if (isset($_GET['page']))
            $page=(int)$_GET['page'];
        else
            $page=1;

        $start=($page-1)*self::$limit;
        $limit=self::$limit;
        $data=$_GET;
        $data['start']=$start;
        $data['limit']=$limit;
        $res=ApiRequest::statistics('v1/log/get',$data);
        if($res->status){
            return $this->render('index', ['logs'=>$res->data->logs,'count'=>$res->data->count]);
        }
    }
}
