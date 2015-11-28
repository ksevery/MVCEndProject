<?php
namespace EndF\HttpContext\HttpRequest;


class RequestForm
{
    public function __get($name)
    {
        return $_POST[$name];
    }
}