<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "market".
 *
 * @property int $id
 * @property string $name
 * @property string $url
 * @property string $image
 * @property int $created_at
 */
class Market extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'market';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'url', 'image', 'created_at'], 'required'],
            [['created_at'], 'integer'],
            [['name', 'url', 'image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'url' => 'Url',
            'image' => 'Image',
            'created_at' => 'Created At',
        ];
    }
	
	public function getPromotions() {
		return $this->hasMany(Promotion::className(), ['market_id'=>'id']);
	}
}
