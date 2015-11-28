<?php
namespace Controllers;

use EndF\BaseController;
use Data\ProjectDb;

class ProjectBaseController extends BaseController
{
    /**
     * @var ProjectDb
     */
    protected $db;

    public function __construct()
    {
        parent::__construct();

        $this->db = new ProjectDb();
    }
}