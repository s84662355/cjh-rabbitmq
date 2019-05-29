<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-05-29
 * Time: 09:55
 */

namespace CustomRabbitmq;

use Illuminate\Console\Command;
use CustomRabbitmq\RabbitmqJob;


class RabbitMQCommand  extends Command
{

    protected $signature = 'RabbitMQCommand  {--c=} {--q=}';

    protected $description = 'RabbitMQCommand Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $connection = $this->option('c');
        $queue      = $this->option('q');
        $job = new RabbitmqJob();
        if(!empty($connection))  $job->select($connection);
        $queue = empty($queue) ? false : $queue;
        $job->consume($queue);

    }





}
