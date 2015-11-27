<?php
namespace EndF;


use EndF\DB\SimpleDB;

class Identity
{
    private $userObject;
    private $db;

    public function __construct($userObject, SimpleDB $db)
    {
        $this->userObject = $userObject;
        $this->db = $db;

        $this->createDbModel();
    }

    private function createDbModel()
    {
        $reflect = new \ReflectionClass($this->userObject);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);

    }


}