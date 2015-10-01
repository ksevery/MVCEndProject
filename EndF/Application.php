<?php
namespace EndF;

require 'Autoloader.php';

class Application
{
    private static $instance = null;
    private $config = null;
    private $frontController = null;

    private function __construct()
    {
        Autoloader::registerNamespace('EndF', dirname(__FILE__ . DIRECTORY_SEPARATOR));
        Autoloader::init();
        $this->config = Config::getInstance();
    }

    public function run()
    {
        if($this->getConfigFolder() == null){
            $this->setConfigFolder('../config');
        }

        $this->frontController = FrontController::getInstance();
        $this->frontController->dispatch();
    }

    /**
     * @return \EndF\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function getConfigFolder()
    {
        return $this->config->getConfigFolder();
    }

    public function setConfigFolder($path)
    {
        $this->config->setConfigFolder($path);
    }


    /**
     * @return \EndF\Application
     */
    public static function getInstance()
    {
        if(self::$instance == null){
            self::$instance = new Application();
        }

        return self::$instance;
    }
}