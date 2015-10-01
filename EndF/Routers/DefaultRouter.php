<?php
namespace EndF\Routers;


class DefaultRouter implements IRouter
{
    public function getUri()
    {
        return urldecode(strtolower(ltrim($_SERVER['REQUEST_URI'], '/')));
    }
}