<?php


namespace App\Services;


use App\Models\Message\Message;
use App\Models\Message\PullPackage;

class PullMessageService
{
    public function getPullPackageFromJson(string $socketMessage) : ?PullPackage
    {
        $decodedMessage = json_decode($socketMessage, true);

        $topic = $decodedMessage['topic'] ?? null;
        $sender = $decodedMessage['sender'] ?? null;
        $event = $decodedMessage['event'] ?? null;
        if ($event === null && !array_key_exists('message', $event)) {
            return null;
        }

        $payloadMessage = $event['message'];
        $type = $payloadMessage['type'] ?? null;
        $data = $payloadMessage['data'] ?? null;

        if ($type === null || $data === null) {
            echo "Wrong event payload message received: {$socketMessage}";
            return null;
        }

        $message = new Message($type, $data);

        return new PullPackage($message, $topic, $sender);
    }
}