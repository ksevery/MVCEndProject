<?php
namespace EndF;


use EndF\DB\SimpleDB;

class BaseController
{
    /**
     * @var Application
     */
    protected $app;
    /**
     * @var View
     */
    protected $view;
    /**
     * @var Config
     */
    protected $config;
    /**
     * @var InputData
     */
    protected $input;

    /**
     * @var DB\SimpleDB
     */
    protected $db;

    protected $session;

    protected $validator;

    public function __construct()
    {
        $this->app = Application::getInstance();
        $this->view = View::getInstance();
        $this->config = $this->app->getConfig();
        $this->input = InputData::getInstance();
        $this->session = $this->app->getSession();
        $this->db = new SimpleDB();
    }

    protected function redirect($uri)
    {
        header("Location: $uri");
    }
}