<?php
namespace EndF;

use EndF\Routers\DefaultRouter;
use EndF\Sessions\NativeSession;

require 'Autoloader.php';

class Application
{
    private static $instance = null;
    private $config = null;
    private $frontController = null;
    /**
     * @var Routers\IRouter
     */
    private $router = null;
    private $dbConnections = array();
    private $session = null;

    private function __construct()
    {
        set_exception_handler(array($this, 'exceptionHandler'));
        Autoloader::registerNamespace('EndF', dirname(__FILE__ . DIRECTORY_SEPARATOR));
        Autoloader::registerAutoLoad();
        $this->config = Config::getInstance();
    }

    public function run()
    {
        if($this->getConfigFolder() == null){
            $this->setConfigFolder('../config');
        }

        $this->frontController = FrontController::getInstance();
        if($this->router == null) {
            $this->router = new DefaultRouter();
        }

        $this->frontController->setRouter($this->router);

        $sess = $this->config->app['session'];
        if(isset($sess['autostart'])){
            if($sess['type'] == 'native'){
                $s = new NativeSession($sess['name'], $sess['lifetime'], $sess['path'], $sess['domain'], $sess['secure']);
            }

            $this->setSession($s);
        }

        $this->frontController->dispatch();
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function setRouter($router)
    {
        $this->router = $router;
    }

    /**
     * @return Sessions\ISession
     */
    public function getSession()
    {
        return $this->session;
    }

    public function setSession(\EndF\Sessions\ISession $session)
    {
        $this->session = $session;
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

    public function getDbConnection($connection = 'default')
    {
        if(!$connection){
            throw new \Exception('No connection identifier given!', 500);
        }

        if(isset($this->dbConnections[$connection])){
            return $this->dbConnections[$connection];
        }

        $connections = $this->getConfig()->database;
        if(!isset($connections[$connection])){
            throw new \Exception('No valid connection identifier given!', 500);
        }

        $dbh = new \PDO($connections[$connection]['connection_uri'], $connections[$connection]['username'], $connections[$connection]['password'], $connections[$connection]['pdo_options']);
        $this->dbConnections[$connection] = $dbh;

        return $dbh;
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

    public function exceptionHandler(\Exception $ex)
    {
        if($this->config && $this->config->app['displayExceptions'] == true){
            echo '<pre>' . print_r($ex, true) . '</pre>';
        } else {
            $this->displayError($ex->getCode());
        }
    }

    public function displayError($errorCode)
    {
        try{
            $view = View::getInstance();
            $view->display('errors.' . $errorCode);
        } catch (\Exception $ex) {
            echo '<h1>' . $errorCode . '</h1>';
            exit;
        }
    }
}