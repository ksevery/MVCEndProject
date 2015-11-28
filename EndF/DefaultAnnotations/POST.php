<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 20:16
 */

namespace EndF\DefaultAnnotations;


use EndF\HttpContext\HttpContext;

class POST extends Annotation
{

    public function performAction(HttpContext $context) : bool
    {
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            return true;
        }

        return false;
    }
}