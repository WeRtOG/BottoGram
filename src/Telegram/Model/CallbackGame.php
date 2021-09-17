<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class CallbackGame extends TelegramModel
{
    public function __construct(
        
    )
    { }

    public static function FromTelegramFormat(object|null|array $Object): self
    {
        return new self();
    }
}

