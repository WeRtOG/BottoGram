<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputMediaVideo extends InputMedia
{
    public string $Type = MediaType::Video;

	public function __construct(
        public InputFile|string $Media,
        public InputFile|string|null $Thumb = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?int $Width = null,
        public ?int $Height = null,
        public ?int $Duration = null,
        public bool $SupportsStreaming = false
    ) { }
}

