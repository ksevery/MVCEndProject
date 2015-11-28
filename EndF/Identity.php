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

        $this->db->prepare("SHOW TABLES LIKE 'users'");
        $this->db->execute();
        if($this->db->affectedRows() > 0) {
            $sql = "ALTER TABLE users ";
            foreach($props as $prop){
                $docComment = $prop->getDocComment();
                $name = $prop->getName();
                if($prop->getDeclaringClass()->getName() != $reflect->getName()){
                    continue;
                }

                if(preg_match('/@ORM\\\Column\s+([\w()]+)/', $docComment, $match)){
                    $type = $match[1];
                } else {
                    $type = 'varchar(100)';
                }
                $this->db->prepare("SHOW COLUMNS FROM users LIKE '$name'");
                $this->db->execute();
                if($this->db->affectedRows() > 0) {
                    $sql .= "MODIFY COLUMN $name $type";
                } else {
                    $sql .= "ADD COLUMN $name $type, ";
                }
            }
        } else {
            $sql = 'CREATE TABLE users( userId CHAR(40) PRIMARY KEY NULL , username varchar(100) NOT NULL, password varchar(100) NOT NULL, email varchar(100) ';

            foreach($props as $prop){
                $docComment = $prop->getDocComment();
                $name = $prop->getName();
                if($prop->getDeclaringClass()->getName() != $reflect->getName()){
                    continue;
                }
                if(preg_match('/@ORM\\\Column\s+([\w()]+)/', $docComment, $match)){
                    $type = $match[1];
                } else {
                    $type = 'varchar(100)';
                }

                $sql .= ",$name $type";
            }

            $sql .= ')';
        }

        $sql = rtrim($sql, ", ");
        $this->db->prepare($sql);
        $this->db->execute();

        $this->db->prepare("SHOW TRIGGERS LIKE 'users'");
        $this->db->execute();
        if($this->db->affectedRows() > 0){
            return;
        } else {
            $this->db->prepare("DELIMITER $$ CREATE TRIGGER t_UserId  BEFORE INSERT ON users  FOR EACH ROW BEGIN  SET NEW.userId = uuid(); end $$
");
            $this->db->execute();
        }
    }


}