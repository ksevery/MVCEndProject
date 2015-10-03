<?php
namespace Controllers;


use EndF\BaseController;

class HomeController extends BaseController
{
    public function index()
    {
        $this->view->appendToLayout('header', 'header');
        $this->view->appendToLayout('meta', 'meta');
        $this->view->appendToLayout('body', 'home');
        $this->view->appendToLayout('footer', 'footer');
        $this->view->displayLayout('Layouts.home');
    }
}