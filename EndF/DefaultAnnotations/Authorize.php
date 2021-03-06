<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 28.11.2015 г.
 * Time: 18:45
 */

namespace EndF\DefaultAnnotations;

use EndF\HttpContext\HttpContext;

class Authorize extends Annotation
{

    public function performAction(HttpContext $context) : bool
    {
        if($context->getSession()->hasSessionKey('token')){
            return true;
        }
        return false;
    }
}