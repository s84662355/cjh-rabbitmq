<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-08-10
 * Time: 11:31
 */

namespace CustomRabbitmq;


class Common
{
    public static  function  encryption(array $args)
    {
       return    base64_encode( json_encode($args));
    }

    public static function  decrypt(string $value)
    {
        return  json_decode(base64_decode($value),true) ;
    }


}