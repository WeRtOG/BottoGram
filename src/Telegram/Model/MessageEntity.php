<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class MessageEntity
{
    public function __construct(
        public string $Type,
        public int $Offset,
        public int $Length,
        public ?string $URL,
        public ?User $User,
        public ?string $Language
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
                URL: $Object->{'url'} ?? null,
                User: User::FromTelegramFormat($Object->{'user'} ?? null),
                Language: $Object->{'language'} ?? null
            );
        }

        return null;
    }
}

?>