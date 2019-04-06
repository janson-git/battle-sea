<?php


namespace App\Models\Message;


class PullPackage
{
    /** @var string */
    private $sender;
    /** @var string */
    private $topic;
    /** @var Message */
    private $message;

    /**
     * PullPackage constructor.
     * @param Message $message
     * @param string $topic
     * @param string $sender
     */
    public function __construct(Message $message, ?string $topic = null, ?string $sender = null)
    {
        $this->message = $message;
        $this->topic = $topic;
        $this->sender = $sender;
    }

    public function getMessage() : Message
    {
        return $this->message;
    }

    public function getTopic() : ?string
    {
        return $this->topic;
    }

    public function getSender() : ?string
    {
        return $this->sender;
    }

    public function __toString()
    {
        return (string) json_encode([
            'sender' => $this->sender,
            'topic' => $this->topic,
            'message' => [
                'type' => $this->message->getType(),
                'data' => $this->message->getData(),
            ],
        ], JSON_UNESCAPED_UNICODE);
    }
}