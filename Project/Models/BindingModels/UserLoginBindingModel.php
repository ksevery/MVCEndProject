<?php
namespace Models\BindingModels;


class UserLoginBindingModel
{
    private $username;
    private $password;

    public function __construct(array $params = null)
    {
        if($params != null){
            $this->setUsername($params['username']);
            $this->setPassword($params['password']);
        }
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = hash('md5', $password);
    }

    public function setAfterRegisterPass($password)
    {
        $this->password = $password;
    }
}