<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultVoice extends TelegramModel
{
    public string $Type = MediaType::Voice;

	public function __construct(
        public string $ID,
        public string $VoiceUrl,
        public string $Title,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?int $VoiceDuration = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null
    ) { }
}

