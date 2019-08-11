<?php
class Apple {
    public function firstMethod() { }
    final protected function secondMethod() { }
    private static function thirdMethod() { }
    public function __construct()
    {
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }
}

$class = new ReflectionClass('Apple');
$methods = $class->getMethods(ReflectionMethod::IS_PUBLIC  );
var_dump($methods);
?>