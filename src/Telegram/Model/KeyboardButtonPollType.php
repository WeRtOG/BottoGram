<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class KeyboardButtonPollType extends TelegramModel
{
    public function __construct(
        public string $Type
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Type: $Object->{'type'}
            );
        }

        return null;
    }
}

