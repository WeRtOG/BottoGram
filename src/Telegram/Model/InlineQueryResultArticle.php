<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultArticle extends InlineQueryResult
{
    public string $Type = MediaType::Article;

	public function __construct(
        public string $ID,
        public string $Title,
        public InputMessageContent $InputMessageContent,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?string $Url = null,
        public bool $HideUrl = false,
        public ?string $Description = null,
        public ?string $ThumbUrl = null,
        public ?int $ThumbWidth = null,
        public ?int $ThumbHeight = null
    )
    { }
}

