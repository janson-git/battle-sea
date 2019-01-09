<?php

namespace App\Providers;

use App\Services\PushMessageService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     * @throws \ZMQSocketException
     */
    public function register()
    {
        // TODO: конфиг сервиса вынести в конфиг
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $socket->connect("tcp://localhost:5555");

        $this->app->instance(PushMessageService::class, new PushMessageService($socket));
    }
}
