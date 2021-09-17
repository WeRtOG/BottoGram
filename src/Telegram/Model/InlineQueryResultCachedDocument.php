<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedDocument extends TelegramModel
{
    public string $Type = MediaType::Document;

	public function __construct(
        public string $ID,
        public string $Title,
        public string $DocumentFileID,
        public ?string $Description = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

