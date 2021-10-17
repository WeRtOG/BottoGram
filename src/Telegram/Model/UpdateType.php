<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class UpdateType
{
    const Unknown = 'unknown';
    const Message = 'message';
    const EditedMessage = 'edited_message';
    const InlineQuery = 'inline_query';
    const PreCheckoutQuery = 'pre_checkout_query';
    const CallbackQuery = 'callback_query';
}

