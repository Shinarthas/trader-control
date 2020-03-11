<?php


namespace console\controllers;


use common\models\Log;
use yii\console\Controller;
use yii\web\ErrorAction;

class ErrorController extends Controller
{
    public function actionIndex(){
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            $statusCode = $exception->statusCode;
            $name = $exception->getName();
            $message = $exception->getMessage();
            return Log::log([
                'exception' => $exception,
                'statusCode' => $statusCode,
                'name' => $name,
                'message' => $message
            ]);
        }
    }
}