<?php

namespace api\v1\renders;

class ResponseRender extends BaseRender
{
    const VALIDATION_ERROR = 'VALIDATION_ERROR';
    const WRONG_TOKEN_ERROR = 'WRONG_TOKEN';
    const INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    const NO_TOKEN_ERROR = 'NO_TOKEN';
    const NO_ITEM_ERROR = 'NO_ITEM';

    /**
     * @param array $data
     * @return array
     */
    public static function success(array $data)
    {
        return [
            'status' => true,
            'data' => $data
        ];
    }

    /**
     * @param $errorCode
     * @param array $date
     * @return array
     */
    public static function failure($errorCode, $date = [])
    {
        $result = [
            'status' => false,
            'error_code' => $errorCode
        ];

        if (!empty($date))
            $result['data'] = $date;

        return $result;
    }
}