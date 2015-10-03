<?php
namespace EndF\Routers;


interface IRouter
{
    public function getUri();
    public function getPost();
    public function getRequestMethod();
}