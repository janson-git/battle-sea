<?php

namespace App\Console\Commands;

use App\Jobs\PlayerShotJob;
use App\Models\Message\PullPackage;
use App\Services\PullMessageService;
use Illuminate\Console\Command;

class PullMessagesListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'messages:pull-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $messageTypeToJobMap = [
        'shot'         => PlayerShotJob::class,
        'cancelBattle' => 'BattleCancelJob'
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pullMessageService = $this->laravel->make(PullMessageService::class);
        $pullDsn = config('zmq.incoming');

        echo $pullDsn . "\n";
        $context = new \ZMQContext();
        $socket = $context->getSocket(\ZMQ::SOCKET_PULL, 'toServer');
        $socket->bind($pullDsn);

        $loop = 100;
        while ($loop) {
            $socketMessages = $socket->recvMulti(\ZMQ::MODE_NOBLOCK);

            if ($socketMessages !== false) {
                echo "PARSE AND HANDLE NEEDED!\n";
                foreach ($socketMessages as $socketMessage) {
                    $pullPackage = $pullMessageService->getPullPackageFromJson($socketMessage);
                    if (!$pullPackage instanceof PullPackage) {
                        echo 'Wrong payload received from socket or some payload fields missed: ' . json_encode($socketMessage);
                        continue;
                    }

                    // route by message!
                    // по $this->messageTypeToJobMap - инициализировать нужный MessageJob,
                    //     и его запускать через dispatch/dispath_now с данными сообщения
                    // TODO: в самом Job - обработка и ответ отправителю/получателю через PushMessageService

                    $message = $pullPackage->getMessage();
                    $messageType = $message->getType();

                    $job = null;
                    if (array_key_exists($messageType, $this->messageTypeToJobMap)) {
                        dispatch_now(
                            new $this->messageTypeToJobMap[$messageType]($pullPackage)
                        );
                    }
                }
                echo json_encode($socketMessages) . "\n";
            }

            sleep(1);
            $loop--;
            echo '.';
        }
    }
}
