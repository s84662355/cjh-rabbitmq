<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-05-29
 * Time: 09:55
 */

namespace CustomRabbitmq;
use Illuminate\Console\Command;


class RabbitMQCommand  extends Command
{

    protected $signature = 'RabbitMQCommand {name?}  {--c=} {--q=}  {--out=}';

    protected $description = ' php artisan RabbitMQCommand ';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $name = $this->argument('name');
        if(!empty($name))
        {
            $file_out = $this->option('out');
            $daemon = new Daemon($name);
            return   $daemon->init( $this,$file_out);
            
        }
        $this->doHandle();
    }

    public function doHandle(  )
    {
        $connection = $this->option('c');
        $queue      = $this->option('q');
        $job = app('RabbitMQJob');
        if(!empty($connection))  $job->select($connection);
        $queue = empty($queue) ? false : $queue;
        $job->consume($queue);
    }





}
