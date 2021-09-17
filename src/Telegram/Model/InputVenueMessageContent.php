<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputVenueMessageContent extends InputMessageContent
{
    public function __construct(
        public float $Longitude,
        public float $Latitude,
        public string $Title,
        public string $Address,
        public ?string $FoursquareID = null,
        public ?string $FoursquareType = null,
        public ?string $GooglePlaceID = null,
        public ?string $GooglePlaceType = null
    )
    { }
}

