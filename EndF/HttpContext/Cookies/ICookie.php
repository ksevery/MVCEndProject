<?php
namespace EndF\HttpContext\Cookies;


interface ICookie
{
    public function __get($name);

    public function __set($name, $value);

    public function hasCookie($name);

    public function removeCookie($name);
}