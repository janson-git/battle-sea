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
            $messages = $socket->recvMulti(\ZMQ::MODE_NOBLOCK);

            echo '.';
            if ($messages !== false) {
                // TODO: parse and handle needed!
                echo "PARSE AND HANDLE NEEDED!\n";
                echo json_encode($messages) . "\n";
            }
            sleep(1);
            $loop--;
        }
    }
}
