<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedPhoto extends TelegramModel
{
    public string $Type = MediaType::Photo;

	public function __construct(
        public string $ID,
        public string $PhotoFileID,
        public ?string $Title = null,
        public ?string $Description = null,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

