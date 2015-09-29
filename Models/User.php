<?php
namespace MvcProject\Models;


class User
{
    private $id;
    private $user;
    private $pass;

    public function __construct($user, $pass, $id = null)
    {
        $this->setUser($user)
            ->setPass($pass)
            ->setId($id);
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    public function setPass($pass)
    {
        $this->pass = $pass;
        return $this;
    }
}