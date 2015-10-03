<?php
namespace Models\BindingModels;


class RegisterUserBindingModel
{
    const DEFAULT_MONEY = 1500;

    private $username;
    private $password;
    private $confirm;

    function __construct(array $params = null)
    {
        if($params != null){
            $this->setPassword($params['password']);
            $this->setUsername($params['username']);
            $this->setConfirm($params['confirm']);
        }
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = hash('md5', $password);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param mixed $confirm
     */
    public function setConfirm($confirm)
    {
        $this->confirm = hash('md5', $confirm);
    }


}