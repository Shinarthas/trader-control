<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "demo_task".
 *
 * @property int $id
 * @property int $company_id
 * @property int $status
 * @property int $sell
 * @property string $tokens_count
 * @property string $currency_one
 * @property string $currency_two
 * @property string $rate
 * @property int $progress
 * @property string $data_json
 * @property int $external_id
 * @property int $time
 * @property int $created_at
 * @property int $loaded_at
 */
class DemoTask extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'demo_task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'status', 'sell', 'tokens_count', 'rate', 'progress', 'data_json', 'external_id', 'time', 'created_at', 'loaded_at'], 'required'],
            [['company_id', 'status', 'sell', 'progress', 'external_id', 'time', 'created_at', 'loaded_at'], 'integer'],
            [['tokens_count', 'rate'], 'number'],
            [['data_json','currency_one','currency_two'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'status' => 'Status',
            'sell' => 'Sell',
            'tokens_count' => 'Tokens Count',
            'rate' => 'Rate',
            'progress' => 'Progress',
            'data_json' => 'Data Json',
            'external_id' => 'External ID',
            'time' => 'Time',
            'created_at' => 'Created At',
            'loaded_at' => 'Loaded At',
            'currency_one' => 'currency_one',
            'currency_two' => 'currency_two',
        ];
    }

    const STATUS_NEW = 0;
    const STATUS_STARTED = 1;
    const STATUS_CREATED = 2;
    const STATUS_PRICE_ERROR = 3;
    const STATUS_CANCELED = 4;
    const STATUS_COMPLETED = 5;
    const STATUS_ACCOUNT_NOT_FOUND = 11;
    public static $statuses = [
        self::STATUS_NEW => 'new',
        self::STATUS_STARTED => 'error',
        self::STATUS_CREATED => 'created',
        self::STATUS_PRICE_ERROR => 'price error',
        self::STATUS_CANCELED => 'canceled by system',
        self::STATUS_COMPLETED => 'completed',
        self::STATUS_ACCOUNT_NOT_FOUND => 'account not found',
    ];

    public function create(){

    }
    public function make(){

    }
}
