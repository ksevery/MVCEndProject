<?php
namespace EndF;

use EndF\DB\SimpleDB;

class FrontController
{
    private static $controllersMethodsAnnotations = array();
    private static $instance = null;

    private $namespace = null;
    private $controller = null;
    private $method = null;
    private $params = array();
    private $customRoutes = array();
    private $scannedControllers = array();
    private $rc = null;
    private $basePath = "Controllers/";
    /**
     * @var Routers\IRouter
     */
    private $router = null;
    private $requestMethod = null;
    private $configRequestMethod = 'get';

    private function __construct()
    {
        $this->scanCustomRoutes();
    }

    public function dispatch()
    {
        if($this->router == null){
            throw new \Exception('Router not set!');
        }

        $uri = $this->router->getUri();

        $this->setNamespace($uri);

        $this->getCustomControllersMethods($uri);

        if($this->controller == null){

            $this->setControllerMethod($uri);
            $this->getDefaultMethodsControllers();
        }

        $this->processController();
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
    public function setRouter(Routers\IRouter $router)
    {
        $this->router = $router;
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
     * @param $uri
     * @throws \Exception
     */
    private function setNamespace(&$uri)
    {
        $routes = Application::getInstance()->getConfig()->routes;

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

    private function getCustomControllersMethods($uri)
    {
        foreach ($this->customRoutes as $route => $value) {
            if (preg_match('/[\s\S]*{.+}[\s\S]*/', $route)) {
                $pattern = preg_replace('/{.+?:string}/', '\w+', $route);
                $pattern = preg_replace('/{.+?:int}/', '\d+', $pattern);
                $pattern = str_replace('/', '\/', $pattern);
                $pattern = '/' . $pattern . '/';
                if (preg_match($pattern, $uri)) {
                    $cleanControllerName = rtrim($this->customRoutes[$route]['Controller'], 'Controller');
                    $this->controller = substr($cleanControllerName, strpos($cleanControllerName, '\\') + 1);
                    $this->method = $this->customRoutes[$route]['Method'];
                    $uri = explode('/', $uri);
                    $this->params = $uri;
                }
            }
        }
    }

    public function getDefaultMethodsControllers()
    {
        if (is_array($this->rc) &&
            isset($this->rc['controllers'])
        ) {
            if(isset($this->rc['controllers'][$this->controller][$this->method]['request_method'])){
                $this->configRequestMethod = $this->rc['controllers'][$this->controller][$this->method]['request_method'];
                if($this->configRequestMethod != $this->requestMethod){
                    throw new \Exception('Wrong request method!', 405);
                }
            }

            if (isset($this->rc['controllers'][$this->controller]['methods'][$this->method])) {
                $this->method = $this->rc['controllers'][$this->controller]['methods'][$this->method];
            }

            if (isset($this->rc['controllers'][$this->controller]['to'])) {
                $this->controller = $this->rc['controllers'][$this->controller]['to'];
            }
        }
    }

    /**
     * @param $uri
     */
    private function setControllerMethod($uri)
    {
        $parts = explode('/', $uri);
        $input = InputData::getInstance();

        if ($parts[0]) {
            $this->controller = array_shift($parts);
            if ($parts[0]) {
                $this->method = array_shift($parts);
                $this->params = array_values($parts);
                $input->setGet($this->params);
            } else {
                $this->method = $this->getDefaultMethod();
            }
        } else {
            $this->controller = $this->getDefaultController();
            $this->method = $this->getDefaultMethod();
        }

        $this->requestMethod = $this->router->getRequestMethod();
    }

    private function scanCustomRoutes()
    {
        if (count($this->scannedControllers) == 0) {
            $controllersFolder = Application::getInstance()->getConfig()->app['namespaces']['Controllers'];
            $allFiles = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($controllersFolder));
            $phpFiles = new \RegexIterator($allFiles, '/\.php$/');
            foreach ($phpFiles as $file) {
                $controllerPath = str_replace('../', '', $file->getPathName());
                $controllerPath = str_replace('..\\', '', $controllerPath);
                $controllerPath = str_replace('.php', '', $controllerPath);
                $normalizedPath = str_replace('/', '\\', $controllerPath);
                if (!array_key_exists($normalizedPath, $this->scannedControllers)) {
                    $this->scannedControllers[] = $normalizedPath;
                    $reflectionController = new \ReflectionClass(new $normalizedPath());
                    $docComment = $reflectionController->getDocComment();
                    if(!empty($docComment)){
                        $annotations = AnnotationParser::getAnnotationClass($docComment);
                        self::$controllersMethodsAnnotations[$reflectionController->getName()] = $annotations;
                    }
                    $methods = $reflectionController->getMethods();
                    foreach ($methods as $method) {
                        preg_match_all('/@Route\("(.*)"\)/', $method->getDocComment(), $matches);
                        $routes = $matches[1];
                        if(empty($matches)){
                            $docComment = $method->getDocComment();
                            if(!empty($docComment)){
                                $annotations = AnnotationParser::getAnnotationClass($docComment);
                                self::$controllersMethodsAnnotations[$reflectionController->getName() . '\\' . $method->getName()] = $annotations;
                            }
                        }
                        foreach ($routes as $route) {
                            if (array_key_exists(strtolower($route), $this->customRoutes)) {
                                throw new \Exception("Route '" . $route . "' already defined in '" .
                                    $this->customRoutes[$route] . "'", 500);
                            }
                            $this->customRoutes[strtolower($route)] =
                                array('Controller' => $normalizedPath, 'Method' => $method->getName());
                        }
                    }
                }
            }
        }
    }

    private function processController()
    {
        $input = InputData::getInstance();
        $input->setGet($this->params);
        $input->setPost($this->router->getPost());
        $file = $this->namespace . DIRECTORY_SEPARATOR . ucfirst($this->controller) . 'Controller';
        $this->controller = $file;
        $realPath = str_replace('\\', DIRECTORY_SEPARATOR, '../' . $file . '.php');
        $realPath = realpath($realPath);
        if (file_exists($realPath) && is_readable($realPath)) {
            $calledController = new $file();
            if(isset(self::$controllersMethodsAnnotations[ucfirst($this->controller)])){
                foreach(self::$controllersMethodsAnnotations[ucfirst($this->controller)] as $ann){
                    if(!$ann->performAction(Application::getInstance()->getHttpContext())){
                        throw new \Exception('Access denied!', 403);
                    }
                }
            }
            if (method_exists($calledController, $this->method)) {
                if ($this->isValidRequestMethod($calledController, $this->method)) {
                    // Create binding model
                    $refMethod = new \ReflectionMethod($calledController, $this->method);
                    $doc = $refMethod->getDocComment();
                    // Validate accessibility
                    $this->ValidateAuthorization($doc);
                    if (preg_match('/@BINDING\s+(\w+)\s+\$/', $doc, $match)) {
                        $bindingModelName = $match[1];
                        $bindingModelsNamespace = Application::getInstance()->getConfig()->app['namespaces']['Models'] . 'BindingModels/';
                        $bindingModelsNamespace = str_replace('../', '', $bindingModelsNamespace);
                        $bindingModelPath = str_replace('/', '\\', $bindingModelsNamespace . $bindingModelName);
                        $bindingReflection = new \ReflectionClass(new $bindingModelPath());
                        $properties = $bindingReflection->getProperties();
                        $params = array();
                        foreach ($properties as $property) {
                            $name = $property->getName();
                            $value = $input->postForDb($name);
                            if ($value === null) {
                                throw new \Exception("Invalid binding model! Property '$name' not found", 400);
                            } else {
                                $params[$name] = $value;
                            }
                        }

                        $bindingModel = new $bindingModelPath($params);
                        Injector::getInstance()->loadDependencies($calledController);
                        $calledController->{strtolower($this->method)}($bindingModel);
                    } else {
                        Injector::getInstance()->loadDependencies($calledController);
                        $calledController->{strtolower($this->method)}();
                    }
                    exit;
                } else {
                    throw new \Exception("Method does not allow '" . ucfirst($this->requestMethod) . "' requests!", 500);
                }
            } else {
                throw new \Exception("'" . $this->method . "' not found in '" . $file . '.php', 404);
            }
        } else {
            throw new \Exception("File '" . $file . '.php' . "' not found!", 404);
        }
    }

    private function isValidRequestMethod($controller, $method)
    {
        $reflectionMethod = new \ReflectionMethod($controller, $method);
        $foundRequestAnnotations = array();
        $comment = strtolower($reflectionMethod->getDocComment());
        if (preg_match('/@get/', $comment)) {
            $foundRequestAnnotations[] = 'get';
        }
        if (preg_match('/@post/', $comment)) {
            $foundRequestAnnotations[] = 'post';
        }
        if (preg_match('/@put/', $comment)) {
            $foundRequestAnnotations[] = 'put';
        }
        if (preg_match('/@delete/', $comment)) {
            $foundRequestAnnotations[] = 'delete';
        }
        if (count($foundRequestAnnotations) != 0) {
            if (count($foundRequestAnnotations) > 1) {
                throw new \Exception('Method cannot have more than 1 request method annotation', 500);
            }
            $request = $foundRequestAnnotations[0];
            if (strtolower($request) != strtolower($this->requestMethod)) {
                return false;
            }
            return true;
        }
        if (strtolower($this->requestMethod) != strtolower($this->configRequestMethod)) {
            return false;
        }
        return true;
    }

    private function ValidateAuthorization($doc)
    {
        $doc = strtolower($doc);
        $notLoggedRegex = '/@notlogged/';
        preg_match($notLoggedRegex, $doc, $matches);
        if ($matches) {
            if (Application::getInstance()->getHttpContext()->getSession()->_login) {
                throw new \Exception("Already logged in!", 400);
            }
        }
        $authorizeRegex = '/@authorize(?:\s+error:\("(.+)"\))?/';
        preg_match($authorizeRegex, $doc, $matches);
        if ($matches) {
            $error = 'Unauthorized!';
            if (isset($matches[1])) {
                $error = ucfirst($matches[1]);
            };
            if (!Application::getInstance()->getHttpContext()->getSession()->_login) {
                throw new \Exception($error, 401);
            }
        }
        $adminRegex = '/@admin/';
        preg_match($adminRegex, $doc, $matches);
        if ($matches) {
            if (!SimpleDB::isAdmin()) {
                throw new \Exception("Admin access only!", 401);
            }
        }
        $roleRegex = '/@role\s*\("(.+)"\)/';
        preg_match($roleRegex, $doc, $matches);
        if (isset($matches[1])) {
            $role = $matches[1];
            if (!SimpleDB::hasRole($role) && !SimpleDB::isAdmin()) {
                $role = ucfirst($role);
                throw new \Exception("$role access only!", 401);
            }
        }
    }
}