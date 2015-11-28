<?php
namespace EndF;


use EndF\DefaultAnnotations\Annotation;

class AnnotationParser
{
    public static function getAnnotationClass(string $documentComment)
    {
        $annotations = array();
        $parts = explode('*', $documentComment);
        foreach($parts as $part){
            $value = trim($part, '*/\ ');
            if(!empty($value)){
                if(preg_match('/@\w+[(]?(.*)?[)]?/', $value, $match)){
                    preg_match('/@\w+[\s\b(]?/', $value, $classMatch);
                    $class = trim($classMatch, '@ (');
                    $obj = new $class($match[1]);
                    if($obj instanceof Annotation){
                        $annotations[] = $obj;
                    }
                }
            }
        }

        return $annotations;
    }
}