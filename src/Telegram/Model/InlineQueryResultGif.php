<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultGif extends InlineQueryResult
{
    public string $Type = MediaType::Gif;

	public function __construct(
        public string $ID,
        public string $GifUrl,
        public string $ThumbUrl,
        public ?int $GifWidth = null,
        public ?int $GifHeight = null,
        public ?int $GifDuration = null,
        public ?string $ThumbMimeType = null,
        public ?string $Title = null,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

