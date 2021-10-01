<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Animation extends Document
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $Width,
        public int $Height,
        public int $Duration,
        public ?PhotoSize $Thumb,
        public ?string $FileName,
        public ?string $MimeType,
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
                Duration: $Object->{'duration'},
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'} ?? null),
                FileName: $Object->{'file_name'} ?? null,
                MimeType: $Object->{'mime_type'} ?? null,
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}