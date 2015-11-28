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

        $this->db->prepare("SHOW TABLES LIKE 'usersRoles'");
        $this->db->execute();
        if($this->db->affectedRows() <= 0) {
            $sql = "CREATE TABLE usersRoles(roleId INT PRIMARY KEY AUTO_INCREMENT, roleName varchar(50), rolePriority INT)";
            $this->db->prepare($sql);
            $this->db->execute();

            $sql = "INSERT INTO usersRoles(roleName, rolePriority) VALUES('User', 0)";
            $this->db->prepare($sql);
            $this->db->execute();
        }

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
            $sql = 'CREATE TABLE users( userId CHAR(40) PRIMARY KEY NULL , username varchar(100) NOT NULL, password varchar(100) NOT NULL, email varchar(100), roleId INT  ';

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

            $sql .= ', FOREIGN KEY (roleId) REFERENCES usersRoles(roleId))';
        }

        $sql = rtrim($sql, ", ");
        $this->db->prepare($sql);
        $this->db->execute();

        $this->db->prepare("SHOW TRIGGERS LIKE 'users'");
        $this->db->execute();
        if($this->db->affectedRows() > 0){
            return;
        } else {
            $sql = "DELIMITER #" .
                PHP_EOL . "CREATE TRIGGER `t_UserId` BEFORE INSERT ON `users` FOR EACH ROW" .
                PHP_EOL . "BEGIN" .
                PHP_EOL . "SET NEW.userId = uuid();" .
                PHP_EOL . "END#";
            $this->db->prepare($sql);
            $this->db->execute();
        }
    }


}