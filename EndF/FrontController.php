<?php
namespace EndF;

use EndF\Routers\DefaultRouter;

class FrontController
{
    private static $instance = null;

    private $namespace = null;
    private $controller = null;
    private $method = null;
    private $params = array();
    private $rc = null;
    private $basePath = "Controllers/";
    private $router = null;

    private function __construct()
    {

    }

    public function dispatch()
    {
        $router = new DefaultRouter();
        $uri = $router->getUri();
        $routes = Application::getInstance()->getConfig()->routes;
        $this->setNamespace($routes, $uri);

        $parts = explode('/', $uri);

        if($parts[0]){
            $this->controller = array_shift($parts);
            if($parts[0]){
                $this->method = array_shift($parts);
            } else {
                $this->method = $this->getDefaultMethod();
            }
        } else {
            $this->controller = $this->getDefaultController();
            $this->method = $this->getDefaultMethod();
        }

        if(is_array($this->rc) &&
            isset($this->rc['controllers']))
        {
            if(isset($this->rc['controllers'][$this->controller]['methods'][$this->method])){
                $this->method = $this->rc['controllers'][$this->controller]['methods'][$this->method];
            }

            if(isset($this->rc['controllers'][$this->controller]['to'])){
                $this->controller = $this->rc['controllers'][$this->controller]['to'];
            }
        }

        $f = $this->namespace . DIRECTORY_SEPARATOR . ucfirst($this->controller) . 'Controller';
        $newController = new $f();
        $newController->{$this->method}();
    }

    public function getDefaultController()
    {
        $config = Application::getInstance()->getConfig();
        if(isset($config->app['default_controller'])) {
            $controller = Application::getInstance()->getConfig()->app['default_controller'];
            if ($controller) {
                return $controller;
            }
        }

        return 'Index';
    }

    public function getDefaultMethod()
    {
        $config = Application::getInstance()->getConfig();
        if(isset($config->app['default_method'])) {
            $method = Application::getInstance()->getConfig()->app['default_method'];
            if ($method) {
                return $method;
            }
        }

        return 'index';
    }

    /**
     * @return Routers\IRouter
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @param Routers\IRouter $router
     */
    public function setRouter($router)
    {
        if($router == null){
            $this->router = new DefaultRouter();
        } else {
            $this->router = $router;
        }
    }

    public static function getInstance()
    {
        if(self::$instance == null){
            self::$instance = new FrontController();
        }

        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @param mixed $controller
     * @return FrontController
     */
    public function setController($controller)
    {
        $controller = $this->basePath . ucfirst(strtolower($controller)) . "Controller";
        if(!class_exists($controller)){
            throw new \InvalidArgumentException("The controller '$controller' has not been defined.");
        }

        $this->controller = $controller;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $method
     * @return FrontController
     */
    public function setMethod($method)
    {
        $reflector = new \ReflectionClass($this->controller);
        if (!$reflector->hasMethod($method)) {
            throw new \InvalidArgumentException("The action '$method' has not been defined.");
        }

        $this->method = $method;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return FrontController
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param $routes
     * @param $uri
     * @throws \Exception
     */
    private function setNamespace($routes, &$uri)
    {
        if (is_array($routes) && count($routes) > 0) {
            foreach ($routes as $route => $value) {
                // Checks if beginning of uri is same as some route. If it's not simple - checks position of route with /.
                if (stripos($uri, $route) === 0 &&
                    ($uri == $route || stripos($uri, $route . '/') === 0) &&
                    isset($value['namespace'])
                ) {
                    $this->namespace = $value['namespace'];
                    $uri = substr($uri, strlen($route) + 1);
                    $this->rc = $value;
                    break;
                }
            }
        } else {
            throw new \Exception('Routes config file not defined!', 500);
        }

        if ($this->namespace == null && isset($routes['*']['namespace'])) {
            $this->namespace = $routes['*']['namespace'];
            $this->rc = $routes['*'];
        } else if ($this->namespace == null && !isset($routes['*']['namespace'])) {
            throw new \Exception('No default namespace set!', 500);
        }
    }
}