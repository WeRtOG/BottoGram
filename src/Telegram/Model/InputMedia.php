<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputMedia extends TelegramModel
{
	public function __construct(
        public string $Type,
        public InputFile|string $Media,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null
    ) { }
}

