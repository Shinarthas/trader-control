<?php

namespace api\v1\renders;

class BaseRender
{
    public static function render($entities)
    {

    }

    public static function dateFormat($timestamp)
    {
        return date('c', $timestamp);
    }
}