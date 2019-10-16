<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;

use common\models\Market;

use common\components\ApiRequest;

/**
 * Site controller
 */
class SiteController extends Controller
{

	public function beforeAction($action)
	{   
		$this->enableCsrfValidation = false;

		return parent::beforeAction($action);
	}
	
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
		$markets = Market::find()->all();
		
        return $this->render('index', ['markets'=>$markets]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
	
	public function actionApiChecker() {
		$responce = '';
	
		if(isset($_POST['go']))
			$responce = ApiRequest::request($_POST['server'], $_POST['action'], json_decode($_POST['data']));
		
		return $this->render("api-checker", ['responce'=>$responce]);
	}
}
