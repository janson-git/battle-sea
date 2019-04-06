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
        $pushDsn = config('zmq.outgoing');

        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'toClient');
        $pushSocket = $socket->connect($pushDsn);

        var_dump($pushSocket->getEndpoints());

        $this->app->instance(PushMessageService::class, new PushMessageService($pushSocket));
    }
}
