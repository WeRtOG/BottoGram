<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class PassportElementError extends TelegramModel
{
    public function __construct(
        public string $Source,
        public string $Type
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Source: $Object->{'source'},
                Type: $Object->{'type'}
            );
        }

        return null;
    }
}

