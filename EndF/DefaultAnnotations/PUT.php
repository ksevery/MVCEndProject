<?php
namespace EndF\DefaultAnnotations;

use EndF\HttpContext\HttpContext;

class PUT extends Annotation
{

    public function performAction(HttpContext $context) : bool
    {
        if($_SERVER['REQUEST_METHOD'] == 'PUT'){
            return true;
        }

        return false;
    }
}