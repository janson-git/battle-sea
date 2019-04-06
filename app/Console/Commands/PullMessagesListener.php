<?php

namespace App\Console\Commands;

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
        'hit'          => 'BattleHitJob',
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
                    $decodedMessage = json_decode($socketMessage, true);

                    $topic = $decodedMessage['topic'] ?? null;
                    $event = $decodedMessage['event'] ?? null;
                    if ($event === null && !array_key_exists('message', $event)) {
                        echo "Wrong socket event received: {$socketMessage}";
                        continue;
                    }

                    $message = $event['message'];
                    $type = $message['type'] ?? null;
                    $data = $message['data'] ?? null;

                    if ($type === null || $data === null) {
                        echo "Wrong event message received: {$socketMessage}";
                        continue;
                    }

                    // TODO: route by message!
                    // TODO: по $this->messageTypeToJobMap - инициализировать нужный MessageJob,
                    // TODO:    и его запускать через dispatch/dispath_now с данными сообщения
                    // TODO: в самом Job - обработка и ответ отправителю/получателю через PushMessageService

                }
                echo json_encode($socketMessages) . "\n";
            }

            sleep(1);
            $loop--;
            echo '.';
        }
    }
}
