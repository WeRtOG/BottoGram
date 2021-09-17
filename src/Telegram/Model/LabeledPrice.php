<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class LabeledPrice extends TelegramModel
{
    public function __construct(
        public string $Label,
        public int $Amount
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Label: $Object->{'text'},
                Amount: $Object->{'voter_count'}
            );
        }

        return null;
    }
}

