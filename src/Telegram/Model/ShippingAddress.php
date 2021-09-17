<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class ShippingAddress extends TelegramModel
{
    public function __construct(
        public string $CountryCode,
        public string $State,
        public string $City,
        public string $StreetLine1,
        public string $StreetLine2,
        public string $PostCode
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                CountryCode: $Object->{'country_code'},
                State: $Object->{'state'},
                City: $Object->{'city'},
                StreetLine1: $Object->{'street_line1'},
                StreetLine2: $Object->{'street_line2'},
                PostCode: $Object->{'post_code'}
            );
        }

        return null;
    }
}

