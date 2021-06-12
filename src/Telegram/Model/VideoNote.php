<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class VideoNote
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $Width,
        public int $Height,
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
                Width: $Object->{'width'},
                Height: $Object->{'height'},
                Length: $Object->{'length'},
                Duration: $Object->{'duration'},
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'}),
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}

?>