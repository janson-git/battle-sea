<?php

namespace App\Services;

use App\Models\Message\Message;
use App\Models\Message\PushPackage;

/**
 * Class PushMessageService
 * Service to push messages to push-server for handle.
 */
class PushMessageService
{
    /** @var \ZMQSocket  */
    private $socket;

    public function __construct(\ZMQSocket $socket)
    {
        $this->socket = $socket;
    }

    /**
     * @param string|array $userIds
     * @param Message $message
     * @throws \ZMQSocketException
     */
    public function sendToUsers($userIds, Message $message) : void
    {
        $package = new PushPackage($message, $userIds);
        $this->socket->send($package);
    }

    /**
     * @param Message $message
     * @throws \ZMQSocketException
     */
    public function sendToAllUsers(Message $message) : void
    {
        $package = new PushPackage($message, PushPackage::SEND_TO_ALL);
        $this->socket->send($package);
    }
}