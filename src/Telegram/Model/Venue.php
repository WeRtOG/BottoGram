<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Venue extends TelegramModel
{
    public function __construct(
        public Location $Location,
        public string $Title,
        public string $Address,
        public ?string $FoursquareID,
        public ?string $FoursquareType,
        public ?string $GooglePlaceID,
        public ?string $GooglePlaceType
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Location: Location::FromTelegramFormat($Object->{'location'}),
                Title: $Object->{'title'},
                Address: $Object->{'address'},
                FoursquareID: $Object->{'foursquare_id'} ?? null,
                FoursquareType: $Object->{'foursquare_type'} ?? null,
                GooglePlaceID: $Object->{'google_place_id'} ?? null,
                GooglePlaceType: $Object->{'google_place_type'} ?? null
            );
        }

        return null;
    }
}

