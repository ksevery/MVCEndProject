<?php
namespace Controllers\Admin;


use EndF\BaseController;

class UsersController extends BaseController
{
    /**
     * @PUT
     * @Admin
     * @param $userId
     */
    public function update($userId)
    {
        echo 'changed';
    }
}