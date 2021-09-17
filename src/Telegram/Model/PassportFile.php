<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class PassportFile extends TelegramModel
{
    public function __construct(
        public string $FileID,
        public string $FileUniqueID,
        public int $FileSize,
        public DateTime $FileDate
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                FileID: $Object->{'file_id'},
                FileUniqueID: $Object->{'file_unique_id'},
                FileSize: $Object->{'file_size'},
                FileDate: DateTime::createFromFormat('U', $Object->{'file_date'})
            );
        }

        return null;
    }
}

