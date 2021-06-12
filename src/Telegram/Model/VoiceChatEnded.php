<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class VoiceChatEnded
{
    public function __construct(
        public int $Duration
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Duration: $Object->{'duration'}
            );
        }

        return null;
    }
}

?>