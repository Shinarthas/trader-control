<?php

namespace common\models;

use common\components\ApiRequest;
use Yii;

/**
 * This is the model class for table "account".
 *
 * @property int $id
 * @property int $type
 * @property int $check_balance
 * @property string $name
 */
class Account extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'name'], 'required'],
            [['type','check_balance'], 'integer'],
            [['name','label'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
            'check_balance' => 'Check balance for this Account',
            'name' => 'Name',
        ];
    }

    public function getBalance(){
        $res=ApiRequest::statistics('v1/account/get-balance',['id'=>$this->id]);
        if($res->status)
            return $res->data;
        return 0;
    }


}
