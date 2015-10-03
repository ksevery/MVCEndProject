<?php
namespace EndF\Routers;


class DefaultRouter implements IRouter
{
    public function getUri()
    {
        return trim(urldecode(strtolower(ltrim($_SERVER['REQUEST_URI'], '/'))));
    }

    public function getPost()
    {
        return $_POST;
    }

    public function getRequestMethod()
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}