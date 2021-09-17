<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class MessageEntity extends TelegramModel
{
    public function __construct(
        public string $Type,
        public int $Offset,
        public int $Length,
        public ?string $Url = null,
        public ?User $User = null,
        public ?string $Language = null
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Type: $Object->{'type'},
                Offset: $Object->{'offset'},
                Length: $Object->{'length'},
                Url: $Object->{'url'} ?? null,
                User: User::FromTelegramFormat($Object->{'user'} ?? null),
                Language: $Object->{'language'} ?? null
            );
        }

        return null;
    }
}

