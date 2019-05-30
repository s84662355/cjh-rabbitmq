<?php
/**
 * Created by PhpStorm.
 * User: chenjiahao
 * Date: 2019-05-29
 * Time: 09:54
 */

namespace CustomRabbitmq;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;


class RabbitMQServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $commands = [
         RabbitMQCommand::class,
    ];


    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'RabbitMQ-config');
        }


        $this->app->singleton(
            'RabbitMQJob',
            function (){
                return new RabbitmqJob(config('rabbitmq_job'));
            }
        );

    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

}
