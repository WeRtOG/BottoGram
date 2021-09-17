<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedSticker extends TelegramModel
{
    public string $Type = MediaType::Sticker;

	public function __construct(
        public string $ID,
        public string $StickerFileID,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

