<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-08-10
 * Time: 15:17
 */

namespace CustomRabbitmq;
use \ReflectionClass;
use \ReflectionMethod;

abstract class AbstractRpcServer
{


   public function __call(string $name,array $args)
   {
       $class = new ReflectionClass(__CLASS__);

      /// ReflectionMethod::IS_PUBLIC

       $class->getMethods(ReflectionMethod::IS_PUBLIC );
   }

 ////  abstract public function before_request(string $name,array $args);
}