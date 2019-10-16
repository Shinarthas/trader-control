<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "promotion_account".
 *
 * @property int $id
 * @property int $promotion_id
 * @property int $account_id
 * @property int $created_at
 */
class PromotionAccount extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'promotion_account';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['promotion_id', 'account_id', 'created_at'], 'required'],
            [['promotion_id', 'account_id', 'created_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'promotion_id' => 'Promotion ID',
            'account_id' => 'Account ID',
            'created_at' => 'Created At',
        ];
    }
}
