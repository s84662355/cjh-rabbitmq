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

    public static function encryptionMsg(string $body,array $config = [])
    {
        $data = [
            'body' => base64_encode($body) ,
            'config' => $config,
            'message_id' => date('Ymdhis').uniqid().rand(100,1000000),
        ];
        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    public static function decryptMsg(string  $msg)
    {
        $body = json_decode($msg,true);
        $body['body'] =  base64_decode($body['body']);
        return $body;
    }


}