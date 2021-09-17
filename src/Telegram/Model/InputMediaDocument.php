<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputMediaDocument extends InputMedia
{
    public string $Type = MediaType::Document;

	public function __construct(
        public InputFile|string $Media,
        public InputFile|string|null $Thumb = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public bool $DisableContentTypeDetection = false
    ) { }
}

