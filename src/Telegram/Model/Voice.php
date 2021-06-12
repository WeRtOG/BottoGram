<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Voice extends Document
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $Duration,
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
                Duration: $Object->{'duration'},
                MimeType: $Object->{'mime_type'} ?? null,
                FileSize: $Object->{'file_size'} ?? null
            );
        }

        return null;
    }
}

?>