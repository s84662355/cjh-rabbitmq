<?php

namespace CustomRabbitmq;


abstract class AbstractConsume{

	 abstract public function process_message(string $body) : bool;
}
