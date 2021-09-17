<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class ChatLocation extends TelegramModel
{
    public function __construct(
        public Location $Location,
        public string $Address
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Location: Location::FromTelegramFormat($Object->{'location'}),
                Address: $Object->{'address'}
            );
        }

        return null;
    }
}

