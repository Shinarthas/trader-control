<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "demo_balance".
 *
 * @property int $id
 * @property array $balances
 * @property string $timestamp
 */
class DemoBalance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'demo_balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['balances', 'timestamp'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'balances' => 'Balances',
            'timestamp' => 'Timestamp',
        ];
    }
}
