# cjh-rabbitmq


composer require chenjiahao/rabbitmq

php artisan vendor:publish 
选择
CustomRabbitmq\RabbitMQServiceProvider


在配置文件app.php加入


    'providers' => [
        
         CustomRabbitmq\RabbitMQServiceProvider::class,
         .
         .
         ..
         .
    ],


