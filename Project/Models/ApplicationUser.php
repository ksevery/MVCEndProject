<?php
namespace Models;


use EndF\IdentityUser;

class ApplicationUser extends IdentityUser
{
    /**
     * @ORM\Column varchar(50)
     */
    protected $phoneNumber;

    public function __construct()
    {
        parent::__construct('', '');
    }

    public function getPhone()
    {
        return $this->phoneNumber;
    }
}