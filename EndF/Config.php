<?php
namespace EndF;



class Config
{
    private static $instance = null;

    private $configArray = array();
    private $configFolder = null;

    private function __construct() { }

    public function getConfigFolder()
    {
        return $this->configFolder;
    }

    public function setConfigFolder($configFolder){
        if(!$configFolder){
            throw new \Exception('No config folder path!');
        }

        $realFolder = realpath($configFolder);
        if($realFolder && is_dir($realFolder) && is_readable($realFolder)){
            $this->configArray = array();
            $this->configFolder = $realFolder . DIRECTORY_SEPARATOR;
            $ns = $this->app['namespaces'];
            if(is_array($ns)){
                Autoloader::registerNamespaces($ns);
            }
        }
    }

    public function __get($name)
    {
        if(!isset($this->configArray[$name])){
            $this->includeConfigFile($this->configFolder . $name . '.php');
        }

        if(array_key_exists($name, $this->configArray)){
            return $this->configArray[$name];
        }

        return null;
    }

    private function includeConfigFile($path)
    {
        if(!$path){
            throw new \Exception('No path passed.');
        }

        $file = realpath($path);
        if($file && is_file($file) && is_readable($file)){
            $basename = explode('.php', basename($file))[0];
            $this->configArray[$basename] = include $file;
        } else {
            throw new \Exception('Config file read error: ' . $file);
        }
    }

    public static function getInstance()
    {
        if(self::$instance == null) {
            self::$instance = new Config();
        }

        return self::$instance;
    }
}