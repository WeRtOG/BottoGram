<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultAudio extends InlineQueryResult
{
    public string $Type = MediaType::Audio;

	public function __construct(
        public string $ID,
        public string $AudioUrl,
        public string $Title,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?string $Performer = null,
        public ?int $AudioDuration = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    )
    { }
}

