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

    protected $signature = 'RabbitMQCommand {name?}  {--c=} {--q=} {--out=}';

    protected $description = ' php artisan RabbitMQCommand ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $connection = $this->option('c');
        $queue      = $this->option('q');
        $name = $this->argument('name');

        if(!empty($name))
        {
            $out_file = $this->option('out');
            if(empty( $out_file )){
               echo "缺少out参数";
               exit();
            } 
            $daemon = new Daemon($name);
            $daemon->init( $out_file);
              
 
  
            
        }

        $job = app('RabbitMQJob');
        if(!empty($connection))  $job->select($connection);
        $queue = empty($queue) ? false : $queue;
        $job->consume($queue);
    }





}
