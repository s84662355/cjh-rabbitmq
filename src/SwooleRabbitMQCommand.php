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

    protected $signature = 'SwooleRabbitMQCommand  {--c=} {--q=} {--w=}';

    protected $description = 'SwooleRabbitMQCommand Command description';

    protected $swoole_work = null;

    protected $process_pool = null;

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

        $this->process_pool = new Pool(empty($this->arg['w']) ? 1 : $this->arg['w']);

        $this->process_pool->on('workerStart',[$this,'process_work']);

        $this->swoole_work->start();
        $this->swoole_work->daemon();

    }

    public function work_callback(swoole_process $worker)
    {
        $this->process_pool->start();   
    }

    public function process_work(Pool $pool, int $workerId)
    {

        $process = $pool->getProcess();
        $process->exec("php", [$_SERVER['SCRIPT_FILENAME'],'RabbitMQCommand','--c'.$this->arg['c'],'--q'.$this->arg['q']   ]);
    }





}
