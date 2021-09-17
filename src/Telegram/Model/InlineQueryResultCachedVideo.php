<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedVideo extends InlineQueryResult
{
    public string $Type = MediaType::Video;

	public function __construct(
        public string $ID,
        public string $VideoFileID,
        public string $Title,
        public ?string $Description = null,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

