<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property array $settings
 * @property double $trigger_score
 * @property double $maximal_stake
 * @property array $strategy
 * @property int $timeout
 * @property string $created_at
 */
class Company extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['settings', 'strategy', 'created_at','accounts','name'], 'safe'],
            [['trigger_score', 'maximal_stake'], 'number'],
            [['timeout'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'settings' => 'Settings',// настройки для опр шанса
            'trigger_score' => 'Trigger Score',// граничное значение шанса при котором запустить сделку
            'maximal_stake' => 'Maximal Stake',// минимальный обьем от банка на бирже
            'strategy' => 'Strategy',
            'timeout' => 'Timeout',
            'accounts' => 'Accounts',
            'name' => 'name',
            'created_at' => 'Created At',
        ];
    }

    public function check($stat){
        $executor=new Company();

    }
}
