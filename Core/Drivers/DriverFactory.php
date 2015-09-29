<?php
namespace MvcProject\Core\Drivers;

use MvcProject\Config\DatabaseConfig;

class DriverFactory
{
    public static function create($driver, $dbUser, $dbPass, $dbName, $dbHost)
    {
        switch($driver){
            case DatabaseConfig::DB_DRIVER:
                return new MySQLDriver($dbUser, $dbPass, $dbName, $dbHost);
            default:
                throw new \Exception('Invalid driver!');
        }
    }
}