<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class KeyboardButton extends TelegramModel
{
    public function __construct(
        public string $Text,
        public bool $RequestContact = false,
        public bool $RequestLocation = false,
        public ?KeyboardButtonPollType $RequestPoll = null
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Text: $Object->{'text'},
                RequestContact: $Object->{'request_contact'} ?? false,
                RequestLocation: $Object->{'request_location'} ?? false,
                RequestPoll: KeyboardButtonPollType::FromTelegramFormat($Object->{'request_poll'} ?? null)
            );
        }

        return null;
    }
}

