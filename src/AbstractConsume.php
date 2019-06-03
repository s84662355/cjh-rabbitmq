<?php

namespace CustomRabbitmq;


abstract class AbstractConsume{

	const ACK = 200;
	const REJECT = 300;
	const EXIT = 400;


	abstract public function process_message(string $body) : bool;
}
