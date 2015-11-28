<?php
namespace EndF\DB;

use EndF\Common;
use EndF\Application;

class SimpleDB
{
    protected $_connection = 'default';
    private $_db = null;
    /**
     * @var \PDO
     */
    private static $database = null;
    /**
     * @var \PDOStatement
     */
    private $_statement = null;
    private $_params = array();
    private $_sql;

    public function __construct($connection = null)
    {
        if ($connection instanceof \PDO) {
            $this->_db = $connection;
            self::$database = $connection;
        } else if ($connection != null) {
            $this->_db = Application::getInstance()->getDbConnection($connection);
            self::$database = Application::getInstance()->getDbConnection($connection);
            $this->_connection = $connection;
        } else {
            $this->_db = Application::getInstance()->getDbConnection($this->_connection);
            self::$database = Application::getInstance()->getDbConnection($this->_connection);
        }

        $this->seed();
    }

    public function prepare($sql, $params = array(), $pdoOptions = array())
    {
        $this->_statement = $this->_db->prepare($sql, $pdoOptions);
        $this->_params = $params;
        $this->_sql = $sql;
        return $this;
    }

    public function execute($params = array())
    {
        if ($params) {
            $this->_params = $params;
        }
        $this->_statement->execute($this->_params);
        return $this;
    }

    public function fetchAllAssoc($escape = true)
    {
        $data = $this->_statement->fetchAll(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return false;
        }
        if ($escape) {
            $escaped = array();
            foreach ($data as $elementKey => $elementData) {
                foreach ($elementData as $key => $value) {
                    $escaped[$elementKey][$key] = htmlentities($value);
                }
            }
            return $escaped;
        }
        return $data;
    }

    public function fetchRowAssoc($escape = true)
    {
        $data = $this->_statement->fetch(\PDO::FETCH_ASSOC);
        if ($data === false) {
            return false;
        }
        if ($escape) {
            $escaped = array();
            foreach ($data as $key => $value) {
                $escaped[$key] = htmlentities($value);
            }
            return $escaped;
        }
        return $data;
    }

    public function getLastInsertedId()
    {
        return $this->_db->lastInsertId();
    }

    /**
     * Can be used for custom use of PDO.
     */
    public function getStatement()
    {
        return $this->_statement;
    }

    public static function isAdmin()
    {
        $statement = self::$database->prepare("SELECT isAdmin
                      FROM users
                      WHERE username = ? AND id = ?");
        $statement->bindParam(1, Application::getInstance()->getSession()->_username);
        $statement->bindParam(2, Application::getInstance()->getSession()->_login);
        $statement->execute();
        $response = $statement->fetch(\PDO::FETCH_ASSOC);
        if ($response) {
            return Common::normalize($response['isAdmin'], 'bool');
        }
        return false;
    }

    public static function hasRole($role)
    {
        $col = 'is' . ucfirst($role);
        try {
            $statement = self::$database->prepare("SELECT {$col}
                      FROM users
                      WHERE username = ? AND id = ?");
            $statement->bindColumn(1, $col);
            $statement->bindParam(1, Application::getInstance()->getSession()->_username);
            $statement->bindParam(2, Application::getInstance()->getSession()->_login);
            $statement->execute();
            $response = $statement->fetch(\PDO::FETCH_ASSOC);
            $response = $response['is' . ucfirst($role)];
        } catch (\PDOException $ex) {
            throw new \Exception("Check your db, missing role '$col'");
        }
        if ($response) {
            return Common::normalize($response, 'bool');
        }
        return false;
    }

    public function affectedRows()
    {
        return $this->_statement->rowCount();
    }

    /**
     * Create custom tables and fill them with data.
     */
    public function seed()
    {
        if(!$this->tableExists('usersRoles')) {
            $sql = "CREATE TABLE usersRoles(roleId INT PRIMARY KEY AUTO_INCREMENT, roleName varchar(50), rolePriority INT)";
            $this->execSql($sql);
        }

        if(!$this->tableExists('users')){
            $sql = 'CREATE TABLE users( userId CHAR(40) PRIMARY KEY NULL , username varchar(100) NOT NULL, password varchar(100) NOT NULL, email varchar(100), roleId INT, FOREIGN KEY (roleId) REFERENCES usersRoles(roleId))  ';
            $this->execSql($sql);
        }

        $this->prepare("SHOW TRIGGERS LIKE 'users'");
        $this->execute();
        if($this->affectedRows() > 0){
            return;
        } else {
            $sql = "DELIMITER #" .
                PHP_EOL . "CREATE TRIGGER `t_UserId` BEFORE INSERT ON `users` FOR EACH ROW" .
                PHP_EOL . "BEGIN" .
                PHP_EOL . "SET NEW.`userId` = uuid();" .
                PHP_EOL . "END#";
            $this->prepare($sql);
            $this->execute();
        }
    }

    protected function execSql($sql)
    {
        $this->prepare($sql);
        $this->execute();
    }

    protected function tableExists($tableName) : bool
    {
        $sql = "SHOW TABLES LIKE ?";
        $this->prepare($sql);
        $this->execute(array($tableName));
        if($this->affectedRows() > 0){
            return true;
        }

        return false;
    }
}