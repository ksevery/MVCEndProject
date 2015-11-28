<?php
namespace Controllers;


use EndF\BaseController;
use EndF\Common;

class HomeController extends BaseController
{
    public function index()
    {
        //Common::dump($this->httpContext->getUserData());
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', 'home');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.home');
    }
}