<?php
namespace MvcProject\Core;


use MvcProject\Core\Drivers\DriverFactory;

class Database
{
    private static $instances;
    private $db;

    private function __construct(\PDO $pdo)
    {
        $this->db = $pdo;
    }

    public static function getInstance($instanceName = 'default')
    {
        if(!isset(self::$instances[$instanceName]))
        {
            throw new \Exception('Instance with that name does not exist!');
        }

        return self::$instances[$instanceName];
    }

    public static function setInstance($instanceName, $driver, $user, $pass, $dbName, $host = null)
    {
        $driver = DriverFactory::create($driver, $user, $pass, $dbName, $host);

        $pdo = new \PDO($driver->getDsn(), $user, $pass);

        self::$instances[$instanceName] = new self($pdo);
    }

    /**
     * @param string $statement
     * @param array $driverOptions
     * @return Statement
     */
    public function prepare($statement, array $driverOptions = [])
    {
        $statement = $this->db->prepare($statement, $driverOptions);

        return new Statement($statement);
    }

    public function query($query)
    {
        $this->db->query($query);
    }

    public function lastId($name = null)
    {
        return $this->db->lastInsertId($name);
    }
}