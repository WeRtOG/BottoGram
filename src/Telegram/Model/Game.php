<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Game extends TelegramModel
{
    public function __construct(
        public string $Title,
        public string $Description,
        public Photo $Photo,
        public ?string $Text,
        public ?MessageEntities $TextEntities,
        public ?Animation $Animation
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Title: $Object->{'title'},
                Description: $Object->{'description'},
                Photo: Photo::FromTelegramFormat($Object->{'photo'}),
                Text: $Object->{'text'} ?? null,
                TextEntities: $Object->{'text_entities'} ?? null,
                Animation: $Object->{'animation'}
            );
        }

        return null;
    }
}

