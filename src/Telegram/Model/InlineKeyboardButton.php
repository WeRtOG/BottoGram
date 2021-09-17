<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineKeyboardButton extends TelegramModel
{
    public function __construct(
        public string $Text,
        public ?string $Url = null,
        public ?LoginUrl $LoginUrl = null,
        public ?string $CallbackData = null,
        public ?string $SwitchInlineQuery = null,
        public ?string $SwitchInlineQueryCurrentChat = null,
        public ?CallbackGame $CallbackGame = null,
        public bool $Pay = false
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Text: $Object->{'text'},
                Url: $Object->{'url'} ?? null,
                LoginUrl: LoginUrl::FromTelegramFormat($Object->{'login_url'} ?? null),
                CallbackData: $Object->{'callback_data'} ?? null,
                SwitchInlineQuery: $Object->{'switch_inline_query'} ?? null,
                SwitchInlineQueryCurrentChat: $Object->{'switch_inline_query_current_chat'} ?? null,
                CallbackGame: CallbackGame::FromTelegramFormat($Object->{'callback_game'} ?? null),
                Pay: $Object->{'pay'} ?? false
            );
        }

        return null;
    }
}

