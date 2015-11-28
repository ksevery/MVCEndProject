<?php
namespace EndF;


use EndF\DefaultAnnotations\Annotation;

class AnnotationParser
{
    private static $invalidClasses = [ 'BINDING', 'ROUTE', 'param', 'throws'];

    public static function getAnnotationClass(string $documentComment)
    {
        $annotations = array();
        $parts = explode('*', $documentComment);
        foreach($parts as $part){
            $value = trim($part, '*/ ');

            if(!empty($value)){
                if(preg_match('/@\w+[\\(]?(.*)?[)]?/', $value, $match)){
                    preg_match('/@((\w+[\\\s\b(]?)*)/', $value, $classMatch);
                    $class = rtrim(trim($classMatch[1], '@ ('));
                    if(!in_array($class, self::$invalidClasses)){
                        try{
                            $obj = new $class($match[1]);
                        } catch(\Exception $e) {
                            continue;
                        }
                        if($obj instanceof Annotation){
                            array_push($annotations, $obj);
                        } else {
                            throw new \Exception('Invalid annotation ' . $class, 400);
                        }
                    }
                }
            }
        }

        return $annotations;
    }
}