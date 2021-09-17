<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultDocument extends InlineQueryResult
{
    public string $Type = MediaType::Document;

	public function __construct(
        public string $ID,
        public string $Title,
        public string $DocumentUrl,
        public string $MimeType,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null,
        public ?string $ThumbUrl = null,
        public ?int $ThumbWidth = null,
        public ?int $ThumbHeight = null
    ) { }
}

