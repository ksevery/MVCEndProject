<?php
namespace EndF\DefaultAnnotations;


use EndF\HttpContext\HttpContext;

abstract class Annotation
{
    public abstract function performAction(HttpContext $context) : bool;
}