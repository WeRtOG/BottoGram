<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class PollOption extends TelegramModel
{
    public function __construct(
        public string $Text,
        public int $VoterCount
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Text: $Object->{'text'},
                VoterCount: $Object->{'voter_count'}
            );
        }

        return null;
    }
}

