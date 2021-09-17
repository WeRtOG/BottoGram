<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class LoginUrl extends TelegramModel
{
    public function __construct(
        public string $Url,
        public ?string $ForwardText = null,
        public ?string $BotUsername = null,
        public bool $RequestWriteAccess = false
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Url: $Object->{'url'},
                ForwardText: $Object->{'forward_text'} ?? null,
                BotUsername: $Object->{'bot_username'} ?? null,
                RequestWriteAccess: $Object->{'request_write_access'} ?? false
            );
        }

        return null;
    }
}

