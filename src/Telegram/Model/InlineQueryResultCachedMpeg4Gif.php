<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultCachedMpeg4Gif extends InlineQueryResult
{
    public string $Type = MediaType::Mpeg4Gif;

	public function __construct(
        public string $ID,
        public string $Mpeg4FileID,
        public ?string $Title = null,
        public ?string $Caption = null,
        public ?string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

