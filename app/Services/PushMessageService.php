<?php

namespace App\Services;

use App\Models\PushPackage\PushMessage;
use App\Models\PushPackage\PushPackage;

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
     * @param PushMessage $message
     * @throws \ZMQSocketException
     */
    public function sendToUsers($userIds, PushMessage $message) : void
    {
        $package = new PushPackage($message, $userIds);
        $this->socket->send($package);
    }

    /**
     * @param PushMessage $message
     * @throws \ZMQSocketException
     */
    public function sendToAllUsers(PushMessage $message) : void
    {
        $package = new PushPackage($message, PushPackage::SEND_TO_ALL);
        $this->socket->send($package);
    }
}