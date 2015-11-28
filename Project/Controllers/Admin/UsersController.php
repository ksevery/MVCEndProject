<?php
namespace Controllers\Admin;


use EndF\BaseController;

class UsersController extends BaseController
{
    /**
     * @EndF\DefaultAnnotations\PUT
     * @EndF\DefaultAnnotations\Admin
     */
    public function update($userId)
    {
        echo 'changed';
    }
}