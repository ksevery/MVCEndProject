<?php
namespace EndF;

require 'Autoloader.php';
require 'FrontController.php';

class Application
{
    private static $instance = null;

    private function __construct(){ }

    public function run()
    {
        Autoloader::init();
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