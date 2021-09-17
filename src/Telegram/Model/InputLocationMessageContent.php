<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputLocationMessageContent extends InputMessageContent
{
    public function __construct(
        public float $Longitude,
        public float $Latitude,
        public ?float $HorizontalAccuracy = null,
        public ?int $LivePeriod = null,
        public ?int $Heading = null,
        public ?int $ProximityAlertRadius = null
    )
    { }
}

