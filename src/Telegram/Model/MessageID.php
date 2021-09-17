<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class MessageID extends TelegramModel
{
    public function __construct(
        public int $MessageID
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                MessageID: $Object->{'message_id'}
            );
        }

        return null;
    }
}

