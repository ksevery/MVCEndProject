<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 14:21
 */

namespace EndF\HttpContext\HttpRequest;


class RequestParams
{
    public function __get($name)
    {
        return $_GET[$name];
    }
}