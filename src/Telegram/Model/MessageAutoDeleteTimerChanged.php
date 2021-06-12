<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class MessageAutoDeleteTimerChanged
{
    public function __construct(
        public DateTime $MessageAutoDeleteTime
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                MessageAutoDeleteTime: DateTime::createFromFormat('U', $Object->{'message_auto_delete_time'})
            );
        }

        return null;
    }
}

?>