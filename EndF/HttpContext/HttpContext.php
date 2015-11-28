<?php
namespace EndF\HttpContext;

use EndF\Common;
use EndF\HttpContext\Cookies\HttpCookie;
use EndF\HttpContext\Cookies\ICookie;
use EndF\HttpContext\HttpRequest\Request;
use EndF\HttpContext\Sessions\ISession;
use EndF\HttpContext\Sessions\NativeSession;

class HttpContext
{
    private $request;
    private $cookies;
    private $session;

    public function __construct(ICookie $cookie = null, ISession $session = null, Request $request = null, UserData $user = null)
    {
        $this->cookies = $cookie ?? new HttpCookie();
        $this->session = $session ?? new NativeSession('sess');
        $this->request = $request ?? new Request();
        $this->setUserData($user);
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
        $userData = new UserData();
        if($this->session->hasSessionKey('username')){
            $userData->username = $this->session->username;
        }

        if($this->session->hasSessionKey('role')) {
            $userData->role = $this->session->role;
        }

        if($this->session->hasSessionKey('userId')) {
            $userData->id = $this->session->userId;
        }

        return $userData;
    }

    public function setUserData(UserData $user = null)
    {
        if($user != null){
            $this->session->username = $user->username;
            $this->session->role = $user->role;
            $this->session->userId = $user->id;
        }

        $this->session->userData = $user;
    }

    public function getSession() : ISession
    {
        return $this->session;
    }
}