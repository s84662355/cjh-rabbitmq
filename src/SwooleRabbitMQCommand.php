<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-05-29
 * Time: 09:55
 */

/*
 *
 *
 * 多余的无用的*/
namespace CustomRabbitmq;

use Illuminate\Console\Command;
use CustomRabbitmq\RabbitmqJob;
use swoole_process;
use Swoole\Process\Pool;
use Swoole\Timer;


class SwooleRabbitMQCommand  extends Command
{

    protected $signature = 'SwooleRabbitMQCommand {name} {--c=} {--q=}   ';

    protected $description = 'SwooleRabbitMQCommand Command description';

    protected $swoole_work = null;

    protected $arg = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->swoole_work = new swoole_process([$this,'work_callback'], true);


       // cli_set_process_title( $this->argument('name'));

        $this->arg['c'] = $this->option('c');
        $this->arg['q'] = $this->option('q');
      //  $this->swoole_work->start();
      //  $this->swoole_work->daemon( true, false);

        $daemon = new Daemon($this->argument('name'));
        $daemon->init();



        $this->dohandle();
    }

    public function work_callback(swoole_process $worker)
    {

      //  $worker->exec($_SERVER['_'], [$_SERVER['PWD'].'/'.$_SERVER['SCRIPT_FILENAME'],'RabbitMQCommand','--c='. $this->arg['c'], '--q='. $this->arg['q']]);

        /*
        $connection = $this->arg['c'];
        $queue      = $this->arg['q'];
        $job = app('RabbitMQJob');
        if(!empty($connection))  $job->select($connection);
        $queue = empty($queue) ? false : $queue;
        $job->consume($queue);
        */

      // $this->call('RabbitMQCommand', ['--c' => $this->arg['c'], '--q' => $this->arg['q']]);
      /// $worker->exit();
    }

    public function dohandle()
    {

        file_put_contents( $_SERVER['PWD'].'/dsadasda' ,time().'   ' ,FILE_APPEND  ) ;
        $connection = $this->arg['c'];
        $queue      = $this->arg['q'];
        $job = app('RabbitMQJob');
        if(!empty($connection))  $job->select($connection);
        $queue = empty($queue) ? false : $queue;
        $job->consume($queue);  
    }


}
