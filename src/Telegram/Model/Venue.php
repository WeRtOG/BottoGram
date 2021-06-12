<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Venue
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
                Location: Location::FromTelegramFormat($Object->{'locations'}),
                Title: $Object->{'title'},
                Address: $Object->{'address'},
                FoursquareID: $Object->{'foursquare_id'},
                FoursquareType: $Object->{'foursquare_type'},
                GooglePlaceID: $Object->{'google_place_id'},
                GooglePlaceType: $Object->{'google_place_type'}
            );
        }

        return null;
    }
}

?>