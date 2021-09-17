<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputMediaPhoto extends InputMedia
{
    public string $Type = MediaType::Photo;

	public function __construct(
        public InputFile|string $Media,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null
    ) { }
}

