<?php
namespace EndF;


class Token
{
    private static $_instance;
    private function __construct()
    {
    }
    public static function init()
    {
        if (self::$_instance == null) {
            self::$_instance = new Token();
        }
        return self::$_instance;
    }
    public static function render($samePage = false)
    {
        self::generateToken();
    }
    public static function validates($token)
    {
        $isValid = Application::getInstance()->getHttpContext()->getSession()->_token === $token;
        self::generateToken();
        return $isValid;
    }
    public static function getToken($samePageToken = false)
    {
        if (!$samePageToken) {
            self::generateToken();
        }
        return Application::getInstance()->getHttpContext()->getSession()->_token;
    }
    private static function generateToken()
    {
        Application::getInstance()->getHttpContext()->getSession()->_token = base64_encode(openssl_random_pseudo_bytes(64));
    }
}