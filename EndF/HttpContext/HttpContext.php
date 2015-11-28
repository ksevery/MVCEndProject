<?php
namespace EndF\HttpContext;

use EndF\HttpContext\Cookies\HttpCookie;
use EndF\HttpContext\Cookies\ICookie;
use EndF\HttpContext\HttpRequest\Request;
use EndF\HttpContext\Sessions\ISession;
use EndF\HttpContext\Sessions\NativeSession;

class HttpContext
{
    private $request;
    private $cookies;
    private $userData;
    private $session;

    public function __construct(ICookie $cookie = null, ISession $session = null, Request $request = null, UserData $user = null)
    {
        $this->cookies = $cookie ?? new HttpCookie();
        $this->session = $session ?? new NativeSession('sess');
        $this->request = $request ?? new Request();
        $this->userData = $user;
    }

    public function getRequest() : Request
    {
        return $this->request;
    }

    public function getCookies() : ICookie
    {
        return $this->cookies;
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function setUserData(UserData $user)
    {
        $this->userData = $user;
    }

    public function getSession() : ISession
    {
        return $this->session;
    }
}