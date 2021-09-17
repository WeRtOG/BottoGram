<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class StickerSet extends TelegramModel
{
    public function __construct(
        public string $Name,
        public string $Title,
        public bool $IsAnimated,
        public bool $ContainsMasks,
        public StickerArray $Stickers,
        public ?PhotoSize $Thumb
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Name: $Object->{'name'},
                Title: $Object->{'title'},
                IsAnimated: $Object->{'is_animated'} ?? false,
                ContainsMasks: $Object->{'contains_masks'} ?? false,
                Stickers: StickerArray::FromTelegramFormat($Object->{'stickers'} ?? null),
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'} ?? null)
            );
        }

        return null;
    }
}

