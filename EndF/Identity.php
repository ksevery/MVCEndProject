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

        $sql = 'CREATE TABLE users( userId CHAR(40) PRIMARY KEY, username varchar(100) NOT NULL, password varchar(100) NOT NULL, email varchar(100) ';

        foreach($props as $prop){
            $value = $prop->getValue();
            $name = $prop->getName();
            $type = gettype($value);
            $sql .= ",$name $type";
        }

        $sql .= ')';

        $this->db->prepare($sql);
        $this->db->execute();

        $this->db->prepare("CREATE TRIGGER 't_UserId' BEFORE INSERT ON 'users' FOR EACH ROW BEGIN SET new.userId = uuid(); END");
        $this->db->execute();
    }


}