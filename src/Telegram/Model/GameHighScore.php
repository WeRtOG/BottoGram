<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class GameHighScore extends TelegramModel
{
    public function __construct(
        public int $Position,
        public User $User,
        public int $Score
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Position: $Object->{'position'},
                User: User::FromTelegramFormat($Object->{'user'}),
                Score: $Object->{'score'}
            );
        }

        return null;
    }
}

