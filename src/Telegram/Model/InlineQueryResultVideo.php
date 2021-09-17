<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultVideo extends TelegramModel
{
    public string $Type = MediaType::Video;

	public function __construct(
        public string $ID,
        public string $VideoUrl,
        public string $MimeType,
        public string $ThumbUrl,
        public string $Title,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?int $VideoWidth = null,
        public ?int $VideoHeight = null,
        public ?int $VideoDuration = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

