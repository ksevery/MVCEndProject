<?php
namespace EndF\HttpContext;

use EndF\HttpContext\Cookies\HttpCookie;
use EndF\HttpContext\Cookies\ICookie;
use EndF\HttpContext\Sessions\ISession;
use EndF\HttpContext\Sessions\NativeSession;

class HttpContext
{
    private $request;
    private $cookies;
    private $response;
    private $userData;
    private $session;

    public function __construct(ICookie $cookie = null, ISession $session = null)
    {
        $this->cookies = $cookie ?? new HttpCookie();
        $this->session = $session ?? new NativeSession('sess');
        $this->request = $_POST;
    }

    public function getRequest() : array
    {
        return $this->request;
    }

    public function getResponse() : array
    {
        return $this->response;
    }

    public function getCookies() : ICookie
    {
        return $this->cookies;
    }

    public function getUserData()
    {
        return $this->userData;
    }

    public function getSession() : ISession
    {
        return $this->session;
    }
}