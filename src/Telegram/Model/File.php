<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class File extends TelegramModel
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public ?int $FileSize,
        public ?string $FilePath
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                FileID: $Object->{'file_id'},
                FileUniqueID: $Object->{'file_unique_id'},
                FileSize: $Object->{'file_size'} ?? null,
                FilePath: $Object->{'file_path'} ?? null
            );
        }

        return null;
    }
}

