<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultMpeg4Gif extends TelegramModel
{
    public string $Type = MediaType::Mpeg4Gif;

	public function __construct(
        public string $ID,
        public string $Mpeg4Url,
        public string $ThumbUrl,
        public ?int $Mpeg4Width = null,
        public ?int $Mpeg4Height = null,
        public ?int $Mpeg4Duration = null,
        public ?string $ThumbMimeType = null,
        public ?string $Title = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

