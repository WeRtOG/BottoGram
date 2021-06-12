<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Dice
{
    public function __construct(
        public string $Emoji,
        public int $Value
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Emoji: $Object->{'emoji'},
                Value: $Object->{'value'}
            );
        }

        return null;
    }
}

?>