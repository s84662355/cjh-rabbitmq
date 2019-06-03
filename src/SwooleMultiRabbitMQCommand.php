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
use swoole_process;
use Swoole\Process\Pool;

class SwooleMultiRabbitMQCommand extends Command
{

    protected $signature = 'SwooleMultiRabbitMQCommand {--c=} {--q=} {--w=}';

    protected $description = 'SwooleMultiRabbitMQCommand Command description';

    protected $swoole_work = null;

    protected $arg = [];

    public function __construct()
    {
        parent::__construct();

        $this->swoole_work = new swoole_process([$this,'work_callback'], true);

    }

    public function handle()
    {
 
        $this->arg['c'] = $this->option('c');
        $this->arg['q'] = $this->option('q');
        $this->arg['w'] = (int)$this->option('w');  

        $this->swoole_work->start();
        $this->swoole_work->daemon();

    }

    public function work_callback(swoole_process $worker)
    {
        for ($i=0; $i < $this->arg['w']; $i++) 
        { 
           $this->call('SwooleRabbitMQCommand', ['--c' => $this->arg['c'], '--q' => $this->arg['q']]);
        }
    }
 

}
