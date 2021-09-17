<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputMediaAudio extends InputMedia
{
    public string $Type = MediaType::Audio;

	public function __construct(
        public InputFile|string $Media,
        public InputFile|string|null $Thumb = null,
        public ?string $Caption = null,
        public string $ParseMode = ParseMode::Markdown,
        public ?MessageEntities $CaptionEntities = null,
        public ?int $Duration = null,
        public ?string $Performer = null,
        public ?string $Title = null,
    ) { }
}

