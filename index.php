<?php
require_once __DIR__ . '/vendor/autoload.php';  
//use CustomRabbitmq\test;  
//$a = new test();

//$a->a();



class AAA{
	public $a = '';

	public function A()
	{
		$this->a = "45678909876543456789";
	} 
}


$a = new AAA();


$aaa = function($a){
    $a->A();
};

$aaa($a);

var_dump($a->a);