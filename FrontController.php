<?php
namespace MvcProject;

class FrontController
{
    protected $controller;
    protected $action;
    protected $params;
    protected $basePath = "Controllers/";

    /**
     * @param array $options Can contain elements 'controller', 'action' and 'params'.
     */
    public function __construct(array $options = array())
    {
        if(empty($options)){
            $this->parseUri();
        } else {
            if(isset($options['controller'])){
                $this->setController($options['controller']);
            }

            if(isset($options['action'])){
                $this->setAction($options['action']);
            }

            if(isset($options['params'])){
                $this->setParams($options['params']);
            }
        }
    }

    public function run()
    {
        call_user_func_array(array(new $this->controller, $this->action), $this->params);
    }

    protected function parseUri()
    {
        $uri = $_SERVER['REQUEST_URI'];
        $self = $_SERVER['PHP_SELF'];
        $index = basename($self);

        $directories = str_replace($index, '', $self);

        $requestUri = str_replace($directories, '', $uri);
        var_dump($requestUri);

        $uriParts = explode('/', $requestUri);

        $controller = array_shift($uriParts);
        $action = array_shift($uriParts);
        $params = [];
        while(!empty($uriParts)){
            array_push($params, array_shift($uriParts));
        }

        var_dump($controller);
        $this->setController($controller);
        $this->setAction($action);
        $this->setParams($params);
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
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     * @return FrontController
     */
    public function setAction($action)
    {
        $reflector = new \ReflectionClass($this->controller);
        if (!$reflector->hasMethod($action)) {
            throw new \InvalidArgumentException("The action '$action' has not been defined.");
        }

        $this->action = $action;
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
}