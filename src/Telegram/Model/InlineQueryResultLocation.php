<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultLocation extends TelegramModel
{
    public string $Type = MediaType::Location;

	public function __construct(
        public string $ID,
        public float $Longitude,
        public float $Latitude,
        public ?string $Title = null,
        public ?float $HorizontalAccuracy = null,
        public ?int $LivePeriod = null,
        public ?int $Heading = null,
        public ?int $ProximityAlertRadius = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null,
        public ?string $ThumbUrl = null,
        public ?int $ThumbWidth = null,
        public ?int $ThumbHeight = null
    ) { }
}

