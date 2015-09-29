<?php
namespace MvcProject\Core;


use MvcProject\Config\DatabaseConfig;

class App
{
    /**
     * @var Database
     */
    private $db;

    /**
     * @var User
     */
    private $user = null;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function isLogged()
    {
        return isset($_SESSION['$id']);
    }

    /**
     * @param $username
     * @return bool
     */
    public function userExists($username)
    {
        $result = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $result->execute([ $username ]);

        return $result->rowCount() > 0;
    }

    public function register($username, $password)
    {
        if($this->userExists($username)){
            throw new \Exception("Username is already taken!");
        }

        $result = $this->db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $result->execute([
            $username,
            password_hash($password, PASSWORD_DEFAULT)]);

        if($result->rowCount() > 0){
            return true;
        }

        throw new \Exception("Cannot register user!");
    }
}