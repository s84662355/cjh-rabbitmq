<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-06-22
 * Time: 14:29
 */
require('../vendor/autoload.php');

use CustomRabbitmq\RabbitmqJob;
use CustomRabbitmq\RabbitmqDriver;

$config = include '../config/rabbitmq_job';

$job = new RabbitmqJob($config);


$job->send("fsfds");





