<?php

namespace CustomRabbitmq;


abstract class AbstractConsume{

	 abstract public function process_message(Message $msg) : bool
	 {
       
	 }

}