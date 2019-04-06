<?php

namespace App\Jobs;

use App\Models\Message\PullPackage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class PlayerShotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $package;

    /**
     * Create a new job instance.
     *
     * @param PullPackage $package
     */
    public function __construct(PullPackage $package)
    {
        $this->package = $package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info(__METHOD__ . ' with package: ' . json_encode([
            'topic' => $this->package->getTopic(),
            'sender' => $this->package->getSender(),
            'messageType' => $this->package->getMessage()->getType(),
            'messageData' => $this->package->getMessage()->getData(),
        ]));

        // TODO: handle sender's shot here!
    }
}
