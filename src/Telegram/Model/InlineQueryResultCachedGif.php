<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedGif extends TelegramModel
{
    public string $Type = MediaType::Gif;

	public function __construct(
        public string $ID,
        public string $GifFileID,
        public ?string $Title = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

