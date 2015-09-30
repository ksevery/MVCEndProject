<?php
namespace EndF;

require 'Autoloader.php';
require 'FrontController.php';

class Application
{
    private static $instance = null;

    private function __construct()
    {
        Autoloader::registerNamespace('EndF', dirname(__FILE__ . DIRECTORY_SEPARATOR));
        Autoloader::init();
    }

    public function run()
    {
        $frontController = new FrontController();
        $frontController->run();
    }

    /**
     * @return \EndF\Application
     */
    public static function getInstance()
    {
        if(self::$instance == null){
            self::$instance = new \EndF\Application();
        }

        return self::$instance;
    }
}