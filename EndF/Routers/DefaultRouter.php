<?php
namespace EndF\Routers;


class DefaultRouter
{
    private $controller = null;
    private $method = null;
    private $params = array();

    public function parse()
    {
        var_dump($_SERVER);
        $requestUri = urldecode(strtolower(ltrim($_SERVER['REQUEST_URI'], '/')));

        $parts = explode('\\', $requestUri);
        if(isset($parts[0])){
            $this->controller = ucfirst(array_shift($parts));
            if(isset($parts[1])){
                $this->method = array_shift($parts);
            }
        }
    }

    /**
     * @return null
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * @return null
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }


}