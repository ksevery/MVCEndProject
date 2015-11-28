<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 14:21
 */

namespace EndF\HttpContext\HttpRequest;


class Request
{
    public $params;

    public $form;

    public function __construct()
    {
        $this->params = new RequestParams();
        $this->form = new RequestForm();
    }
}