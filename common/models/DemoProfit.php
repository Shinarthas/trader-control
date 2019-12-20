<?php

namespace common\models;

use DeepCopy\f001\A;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "demo_profit".
 *
 * @property int $id
 * @property double $value
 * @property string $timestamp
 */
class DemoProfit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'demo_profit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['value'], 'number'],
            [['timestamp'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'value' => 'Value',
            'timestamp' => 'Timestamp',
        ];
    }

    public static function create($p){
        if($p>0){
            $profit=new DemoProfit();
            $profit->value=$p;
            $profit->save();

            Log::log(ArrayHelper::toArray($profit),'withdraw','profit');
        }else{
            $profit=new DemoProfit();
            $profit->value=$p;
            $profit->save();

            Log::log(ArrayHelper::toArray($profit),'withdraw','losses');
        }
    }
}
