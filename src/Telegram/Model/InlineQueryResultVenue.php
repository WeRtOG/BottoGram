<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultVenue extends TelegramModel
{
    public string $Type = MediaType::Venue;

	public function __construct(
        public string $ID,
        public float $Longitude,
        public float $Latitude,
        public string $Title,
        public string $Address,
        public ?string $FoursquareID = null,
        public ?string $FoursquareType = null,
        public ?string $GooglePlaceID = null,
        public ?string $GooglePlaceType = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null,
        public ?string $ThumbUrl = null,
        public ?int $ThumbWidth = null,
        public ?int $ThumbHeight = null
    ) { }
}

