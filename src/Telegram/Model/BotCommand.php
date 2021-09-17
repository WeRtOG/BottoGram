<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;
use WeRtOG\BottoGram\Navigation\Command;

class BotCommand extends TelegramModel
{
    public function __construct(
        public string $Command,
        public string $Description
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Command: $Object->{'command'},
                Description: $Object->{'description'}
            );
        }

        return null;
    }
}