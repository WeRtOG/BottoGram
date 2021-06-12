<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class VoiceChatStarted
{
    public function __construct(
        
    )
    { }

    public static function FromTelegramFormat(?object $Object): self
    {
        return new self();
    }
}

?>