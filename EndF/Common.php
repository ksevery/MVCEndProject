<?php
/**
 * Created by PhpStorm.
 * User: konst
 * Date: 2.10.2015 ã.
 * Time: 21:36
 */

namespace EndF;


class Common
{
    public static function normalize($data, $types)
    {
        $types = explode('|', $data);
        if(is_array($types)){
            if(isset($types['int'])){
                $data = (int)$data;
            }

            if(isset($types['float'])){
                $data = (float)$data;
            }

            if(isset($types['double'])){
                $data = (double)$data;
            }

            if(isset($types['bool'])){
                $data = (bool)$data;
            }

            if(isset($types['string'])){
                $data = (string)$data;
            }

            if(isset($types['trim'])){
                $data = trim($data);
            }
        }

        return $data;
    }
}