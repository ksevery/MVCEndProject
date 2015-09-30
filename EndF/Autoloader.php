<?php
namespace EndF;

final class Autoloader
{
    private static $namespaces = array();

    private function __construct() { }

    public static function init()
    {
        spl_autoload_register(array("\EndF\Autoloader", 'autoload'));
//        spl_autoload_register(function($class){
//            $pathParams = explode("\\", $class);
//            $path = implode(DIRECTORY_SEPARATOR, $pathParams);
//            $path = str_replace($pathParams[0], "", $path);
//
//            require_once $_SERVER['DOCUMENT_ROOT'] . 'MVCEndProject' . $path . ".php";
//        });
    }

    public static function autoload($class)
    {
        self::loadClass($class);
    }

    public static function loadClass($class)
    {
        foreach(self::$namespaces as $key => $value){
            if(strpos($class, $key) === 0){
                $classFix = str_replace('\\', DIRECTORY_SEPARATOR, $class);
                $classFix = substr_replace($classFix, $value, 0, strlen($key)) . '.php';
                $file = realpath($classFix);
                if($file && is_readable($file)){
                    include $file;
                } else {
                    throw new \Exception('File cannot be included: ' . $file);
                }

                break;
            }
        }
    }

    public static function registerNamespace($namespace, $path)
    {
        $namespace = trim($namespace);
        if(strlen($namespace) > 0){
            if(!$path){
                throw new \Exception('Invalid path.');
            }

            $path = realpath($path);
            if($path && is_dir($path) && is_readable($path)){
                self::$namespaces[$namespace . '\\'] = $path . DIRECTORY_SEPARATOR;
            } else {
                throw new \Exception('Namespace directory read error: ' . $path);
            }
        } else {
            throw new \Exception('Invalid namespace: ' . $namespace);
        }
    }
}