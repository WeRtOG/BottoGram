<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class VideoNote extends TelegramModel
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $Length,
        public int $Duration,
        public ?PhotoSize $Thumb,
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
                Length: $Object->{'length'},
                Duration: $Object->{'duration'},
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'} ?? null),
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}

