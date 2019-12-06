<?php

namespace common\models;

use common\components\ApiRequest;
use Yii;
use yii\base\Model;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property array $info
 * @property string $message
 * @property string $type
 * @property string $from
 * @property string $created_at
 */
class Log extends Model
{

    public static function log($data,$type=false,$message=false){
        if($type)// error, info, warning
            $data['type']=$type;
        if($message)
            $data['message']=$type;
        $data['from']='control';
        return ApiRequest::statistics('v1/log/index',$data);
    }

    //called as php error handler
    public function logError(){
        $data=func_get_args();
        $data['from']='control';
        $res= ApiRequest::statistics('v1/log/index',$data);
        return $res;
    }
}
