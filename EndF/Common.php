<?php
namespace EndF;


class Common
{
    /**
     * @param $data
     * @param string $types Can contain int|float|double|bool|string|trim|noescape
     * @return bool|float|int|string
     */
    public static function normalize($data, $types)
    {
        $types = explode('|', $types);
        if(is_array($types)){
            if(!isset($types['noescape'])) {
                $data = htmlentities($data);
            }

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

            return $data;
        }

        return htmlentities($data);
    }

}