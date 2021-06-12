<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class VoiceChatScheduled
{
    public function __construct(
        public DateTime $StartDate
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                StartDate: DateTime::createFromFormat('U', $Object->{'start_date'})
            );
        }

        return null;
    }
}

?>