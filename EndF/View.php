<?php
namespace EndF;


class View
{
    private static $_instance = null;
    private $_viewPath = null;
    private $_viewDir = null;
    private $_viewBag = array();
    private $_layoutParts = array();
    private $_layoutData = array();
    private $_extension = '.php';

    private function __construct()
    {
        $this->_viewPath = Application::getInstance()->getConfig()->app['views'];
        if ($this->_viewPath == null) {
            $this->_viewPath = realpath('../Views/');
        }
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new View();
        }
        return self::$_instance;
    }

    public function __get($name)
    {
        return $this->_viewBag[$name];
    }

    public function __set($name, $value)
    {
        $this->_viewBag[$name] = $value;
    }

    public function setViewDirectory($path)
    {
        $path = trim($path);
        if ($path) {
            $path = realpath($path) . DIRECTORY_SEPARATOR;
            if (is_dir($path) && is_readable($path)) {
                $this->_viewDir = $path;
            } else {
                throw new \Exception('Problem with view path', 500);
            }
        } else {
            throw new \Exception('Problem with view path', 500);
        }
    }

    /**
     * Renders given viewModel with the proper View for it or throws exception when
     * View model does not belong to the calling controller.
     * @param $viewModel
     * @throws \Exception
     */
    public function display($viewModel)
    {
        $this->validateViewModel($viewModel);
        $this->_viewBag = $viewModel;
        $this->includeFile($viewModel);
        $file = $this->getViewModelPath($viewModel);
        $path = str_replace('.', DIRECTORY_SEPARATOR, $file);
        $fullPath = $this->_viewDir . $path . $this->_extension;
        $this->includeView($fullPath);
    }

    /**
     * Packages must be with starting big letter, views with starting small letters and separated by dot.
     * @param $name
     * @param bool $returnAsString
     * @return string
     * @throws \Exception
     */
    public function displayLayout($name, $returnAsString = false)
    {
        if (count($this->_layoutParts) > 0) {
            foreach ($this->_layoutParts as $key => $template) {
                $layout = $this->includeFile($template);
                if ($layout) {
                    $this->_layoutData[$key] = $layout;
                }
            }
        }

        if ($returnAsString) {
            return $this->includeFile($name);
        } else {
            echo $this->includeFile($name);
        }
    }

    /**
     * Flexible append method for views. Can be used with ViewModel or string name of the model.
     * When ViewModel used if the caller is not the Views controller exception is thrown.
     * @param $key
     * @param $template string or viewModel
     * @throws \Exception
     */
    public function appendToLayout($key, $template)
    {
        if ($key && $template) {
            if (!is_string($template)) {
                $this->validateViewModel($template);
                $this->_viewBag[$key] = $template;
            }

            $this->_layoutParts[$key] = $template;
        } else {
            throw new \Exception('Layouts require valid key and template!', 500);
        }
    }

    public function getLayoutData($name)
    {
        return $this->_layoutData[$name];
    }

    private function includeFile($file)
    {
        if ($this->_viewDir == null) {
            $this->setViewDirectory($this->_viewPath);
        }

        if (!is_string($file)) {
            $file = $this->getViewModelPath($file);
        }

        $path = str_replace('.', DIRECTORY_SEPARATOR, $file);
        $fullPath = $this->_viewDir . $path . $this->_extension;
        if (file_exists($fullPath) && is_readable($fullPath)) {
            // adds to different buffer
            ob_start();
            $this->includeView($fullPath);
            // returns the buffer as string
            return ob_get_clean();
        } else {
            throw new \Exception('View ' . $file . ' cannot be included', 500);
        }
    }

    private function includeView($path)
    {
        include $path;
    }

    /**
     * @param $template
     * @throws \Exception
     */
    private function validateViewModel($template)
    {
        $trace = debug_backtrace();
        $callerClass = $trace[2]['class'];
        $callerMethod = $trace[2]['function'];
        $callerTokens = explode('\\', $callerClass);
        unset($callerTokens[0]);
        $expected = implode($callerTokens) . ucfirst($callerMethod) . 'ViewModel';
        $tokens = explode('\\', get_class($template));
        unset($tokens[0]);
        unset($tokens[1]);
        $given = implode($tokens);
        if ($expected != $given) {
            throw new \Exception("Controller '" . $callerClass . "' with method '" . $callerMethod .
                "' cannot call ViewModel '" . $given . "' witch is not belonging to him!", 500);
        }
    }

    private function getViewModelPath($file)
    {
        $tokens = explode('\\', get_class($file));
        $tokens[count($tokens) - 1] = strtolower(str_replace('ViewModel', '', $tokens[count($tokens) - 1]));
        unset($tokens[0]);
        unset($tokens[1]);
        $file = implode(DIRECTORY_SEPARATOR, $tokens);
        return $file;
    }
}