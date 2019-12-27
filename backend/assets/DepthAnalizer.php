<?php


namespace backend\assets;


class DepthAnalizer
{
    public static $url="http://itrader.molotovlab.com/prediction";
    public static function getPossibility($symbol){
        $url=self::$url.'?symbol='.$symbol;
        $data=file_get_contents($url);
        $data=json_decode($data);
        return $data->prediction;

    }
}