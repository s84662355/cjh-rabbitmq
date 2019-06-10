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

class SwooleMultiRabbitMQCommand extends Command
{

    protected $signature = 'SwooleMultiRabbitMQCommand {--c=} {--q=} {--w=}';

    protected $description = 'SwooleMultiRabbitMQCommand Command description';

    protected $swoole_work = null;

    protected $swoole_pool = null;

    protected $arg = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->swoole_work = new swoole_process([$this, 'work_callback'], true);

        $this->arg['c'] = $this->option('c');
        $this->arg['q'] = $this->option('q');
        $this->arg['w'] = (int)$this->option('w');

        if($this->arg['w']<1)$this->arg['w']=1;


        $pool = new  Pool($this->arg['w'], SWOOLE_IPC_NONE, 0 );

        $pool->on('workerStart', function (  $pool, int $workerId) {

            $process = $pool->getProcess();
            $process ->exec($_SERVER['_'], [$_SERVER['PWD'].'/'.$_SERVER['SCRIPT_FILENAME'],'SwooleRabbitMQCommand','--c='. $this->arg['c'], '--q='. $this->arg['q']]);


        });

        $pool->start();

      //  $this->swoole_work->start();
     //   $this->swoole_work->daemon(true, true);




    }

    public function work_callback(swoole_process $worker)
    {
        for ($i=0; $i < $this->arg['w']; $i++) 
        {
            $worker->write($i);
            $worker->exec($_SERVER['_'], [$_SERVER['PWD'].'/'.$_SERVER['SCRIPT_FILENAME'],'SwooleRabbitMQCommand','--c='. $this->arg['c'], '--q='. $this->arg['q']]);
            $worker->write($i);
         ///  $this->call('SwooleRabbitMQCommand', ['--c' => $this->arg['c'], '--q' => $this->arg['q']]);
        }
    }
 

}
