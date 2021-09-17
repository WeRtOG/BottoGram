<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Document extends TelegramModel
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
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
                Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'}),
                FileName: $Object->{'file_name'} ?? null,
                MimeType: $Object->{'mime_type'} ?? null,
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}

