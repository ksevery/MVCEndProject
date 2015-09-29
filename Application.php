<?php
namespace MvcProject;

require 'Autoloader.php';
require 'FrontController.php';

class Application
{
    public function run()
    {
        Autoloader::init();
        $frontController = new FrontController();
        $frontController->run();
    }
}