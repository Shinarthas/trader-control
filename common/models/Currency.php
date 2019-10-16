<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "currency".
 *
 * @property int $id
 * @property string $symbol
 * @property int $decimals
 * @property int $type
 * @property string $address
 * @property string $data_json
 * @property int $created_at
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['symbol', 'decimals', 'type', 'address', 'data_json', 'created_at'], 'required'],
            [['decimals', 'type', 'created_at'], 'integer'],
            [['data_json'], 'string'],
            [['symbol', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'symbol' => 'Symbol',
            'decimals' => 'Decimals',
            'type' => 'Type',
            'address' => 'Address',
            'data_json' => 'Data Json',
            'created_at' => 'Created At',
        ];
    }
	
	public function getData($assoc = true)
    {
        return json_decode($this->data_json,$assoc);
    }
	 
    public function setData($data)
    {
        $this->data_json = json_encode($data);
    }
}
