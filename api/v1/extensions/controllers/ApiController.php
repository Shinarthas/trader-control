<?php

namespace api\v1\extensions\controllers;

use Yii;
use yii\web\Controller;

class ApiController extends Controller
{
    protected $_timestamp = null;

    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (Yii::$app->response->format === 'html') {
                Yii::$app->response->format = 'json';
            }

            $this->_timestamp = strtotime(Yii::$app->request->get('timestamp')) ?: 0;

            return true;
        }

        return false;
    }
}