<?php
namespace EndF;


class IdentityRoles
{
    /**
     * @ORM\Column int
     */
    public $roleId;

    /**
     * @ORM\Column varchar(50)
     */
    public $roleName;

    /**
     * @ORM\Column int
     */
    public $rolePriority;
}