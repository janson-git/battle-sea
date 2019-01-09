<?php


namespace App\Models\PushPackage;


class PushPackage
{
    public const SEND_TO_ALL = '*';

    /** @var array */
    private $receivers;
    /** @var PushMessage */
    private $message;

    /**
     * PushPackage constructor.
     * @param string|array $receiver
     * @param PushMessage $message
     */
    public function __construct(PushMessage $message, $receiver = [])
    {
        if (!is_array($receiver)) {
            $receiver = [$receiver];
        }

        if (in_array(self::SEND_TO_ALL, $receiver, true)) {
            $receiver = [self::SEND_TO_ALL];
        }

        $this->receivers = $receiver;
        $this->message = $message;
    }

    public function __toString()
    {
        return (string) json_encode([
            'receivers' => $this->receivers,
            'message' => [
                'type' => $this->message->getType(),
                'data' => $this->message->getData(),
            ],
        ], JSON_UNESCAPED_UNICODE);
    }
}