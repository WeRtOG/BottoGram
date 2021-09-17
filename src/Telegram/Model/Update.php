<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Update extends TelegramModel
{
    public string $Type;

    public function __construct(
        public int $ID,
        public ?Request $Request,
        public ?Message $Message,
        public ?Message $EditedMessage,
        public ?InlineQuery $InlineQuery,
        public ?PreCheckoutQuery $PreCheckoutQuery,
        public ?CallbackQuery $CallbackQuery
    )
    {
        if($Message != null)
            $this->Type = UpdateType::Message;
        
        if($EditedMessage != null)
            $this->Type = UpdateType::EditedMessage;

        if($InlineQuery != null)
            $this->Type = UpdateType::InlineQuery;

        if($PreCheckoutQuery != null)
            $this->Type = UpdateType::PreCheckoutQuery;

        if($CallbackQuery != null)
            $this->Type = UpdateType::CallbackQuery;

        
        //print_r($this->EditedMessage);
        //print_r($this->InlineQuery);
        //print_r($this->PreCheckoutQuery);
        //print_r($this->CallbackQuery);
		//print_r($this->Message);
    }

    public static function FromTelegramFormat(?object $Object, ?Request $Request = null): ?self
    {
        if($Object != null)
        {
            $IsChannelPost = property_exists($Object, 'channel_post') || property_exists($Object, 'edited_channel_post');
            $MessageObject = $IsChannelPost ? $Object->{'channel_post'} ?? null : ($Object->{'message'} ?? null);
            $EditedMessageObject = $IsChannelPost ? $Object->{'edited_channel_post'} ?? null : ($Object->{'edited_message'} ?? null);

            return new self(
                ID: $Object->{'update_id'},
                Request: $Request,
                Message: Message::FromTelegramFormat($MessageObject, $IsChannelPost),
                EditedMessage: Message::FromTelegramFormat($EditedMessageObject, $IsChannelPost),
                InlineQuery: InlineQuery::FromTelegramFormat($Object->{'inline_query'} ?? null),
                PreCheckoutQuery: PreCheckoutQuery::FromTelegramFormat($Object->{'pre_checkout_query'} ?? null),
                CallbackQuery: CallbackQuery::FromTelegramFormat($Object->{'callback_query'} ?? null)
            );
        }

        return null;
    }
}

