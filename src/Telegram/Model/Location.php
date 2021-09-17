<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Location extends TelegramModel
{
    public function __construct(
        public float $Longitude,
        public float $Latitude,
        public ?float $HorizontalAccuracy,
        public ?int $LivePeriod,
        public ?int $Heading,
        public ?int $ProximityAlertRadius
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Longitude: $Object->{'longitude'},
                Latitude: $Object->{'latitude'},
                HorizontalAccuracy: $Object->{'horizontal_accuracy'} ?? null,
                LivePeriod: $Object->{'live_period'} ?? null,
                Heading: $Object->{'heading'} ?? null,
                ProximityAlertRadius: $Object->{'proximity_alert_radius'} ?? null
            );
        }

        return null;
    }
}

