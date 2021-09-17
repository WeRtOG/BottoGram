<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class ShippingOption extends TelegramModel
{
    public function __construct(
        public string $ID,
        public string $Title,
        public LabeledPriceArray $Prices
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
                Title: $Object->{'title'},
                Prices: LabeledPriceArray::FromTelegramFormat($Object->{'prices'} ?? null)
            );
        }

        return null;
    }
}

