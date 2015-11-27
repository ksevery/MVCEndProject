<?php
namespace EndF\HttpContext\Cookies;

class HttpCookie implements ICookie
{
    // Seconds in day set as default.
    private $expirationTime = 86400;

    public function __get($name)
    {
        return $_COOKIE[$name];
    }

    public function __set($name, $value)
    {
        setcookie($name, $value, time() + $this->expirationTime);
    }

    public function removeCookie($name)
    {
        if(isset($_COOKIE[$name])){
            unset($_COOKIE[$name]);
        }

        setcookie($name, '123', 1);
    }

    public function hasCookie($name) : bool
    {
        return isset($_COOKIE[$name]);
    }
}