<?php

namespace EndF\DefaultAnnotations;

use EndF\HttpContext\HttpContext;

class GET extends Annotation
{

    public function performAction(HttpContext $context) : bool
    {
        if($_SERVER['REQUEST_METHOD'] == 'GET'){
            return true;
        }

        return false;
    }
}