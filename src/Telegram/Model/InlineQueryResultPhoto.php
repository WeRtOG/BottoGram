<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultPhoto extends InlineQueryResult
{
    public string $Type = MediaType::Photo;

	public function __construct(
        public string $ID,
        public string $PhotoUrl,
        public string $ThumbUrl,
        public ?int $PhotoWidth,
        public ?int $PhotoHeight,
        public ?string $Title = null,
        public ?string $Description = null,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

