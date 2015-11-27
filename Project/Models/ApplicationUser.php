<?php
namespace Project\Models;


use EndF\IdentityUser;

class ApplicationUser extends IdentityUser
{
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