<?php
namespace EndF;


class IdentityUser
{
    /**
     * @ORM\Column char(40)
     */
    protected $id;

    /**
     * @ORM\Column varchar(50)
     */
    protected $username;

    /**
     * @ORM\Column varchar(100)
     */
    protected $password;

    /**
     * @ORM\Column varchar(100)
     */
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