<?php
namespace EndF;


class IdentityUser
{
    protected $id;

    protected $username;

    protected $password;

    protected $email;

    public function __construct(string $username, string $password, string $email = null)
    {
        $this->username = $username;
        $this->password = hash('md5', $password);
        $this->email = $email;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getUsername() : string
    {
        return $this->username;
    }

    public function getPassword() : string
    {
        return $this->password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }
}