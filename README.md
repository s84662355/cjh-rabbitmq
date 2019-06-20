# cjh-rabbitmq


composer require chenjiahao/rabbitmq

在配置文件app.php加入


    'providers' => [
        
         CustomRabbitmq\RabbitMQServiceProvider::class,
         .
         .
         ..
         .
    ],


php artisan vendor:publish 
选择
CustomRabbitmq\RabbitMQServiceProvider

 

设置进程名称eeeee
 php artisan RabbitMQCommand eeeee


 app('RabbitMQJob')->send('sfs');