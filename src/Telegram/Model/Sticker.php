<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Sticker
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $Width,
        public int $Height,
        public bool $IsAnimated,
        public ?PhotoSize $Thumb,
        public ?string $Emoji,
        public ?string $SetName,
        public ?MaskPosition $MaskPosition,
        public ?int $FileSize
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                FileID: $Object->{'file_id'},
                FileUniqueID: $Object->{'file_unique_id'},
                Width: $Object->{'width'},
                Height: $Object->{'height'},
                IsAnimated: $Object->{'is_animated'},
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'} ?? null),
                Emoji: $Object->{'emoji'} ?? null,
                SetName: $Object->{'set_name'} ?? null,
                MaskPosition: MaskPosition::FromTelegramFormat($Object->{'mask_position'}),
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}

?>