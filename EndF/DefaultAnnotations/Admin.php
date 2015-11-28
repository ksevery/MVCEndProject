<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 20:22
 */

namespace EndF\DefaultAnnotations;


use EndF\HttpContext\HttpContext;

class Admin extends Annotation
{

    public function performAction(HttpContext $context) : bool
    {
        return true;
    }
}