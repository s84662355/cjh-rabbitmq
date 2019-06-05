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

class SwooleRabbitMQCommand  extends Command
{

    protected $signature = 'SwooleRabbitMQCommand  {--c=} {--q=}  ';

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
        $this->arg['c'] = $this->option('c');
        $this->arg['q'] = $this->option('q');
        $this->swoole_work->start();
        $this->swoole_work->daemon();
    }

    public function work_callback(swoole_process $worker)
    {
       $this->call('RabbitMQCommand', ['--c' => $this->arg['c'], '--q' => $this->arg['q']]);
       $worker->exit();
    }


}
