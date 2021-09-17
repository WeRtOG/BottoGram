<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class OrderInfo extends TelegramModel
{
    public function __construct(
        public ?string $Name,
        public ?string $PhoneNumber,
        public ?string $Email,
        public ?ShippingAddress $ShippingAddress
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Name: $Object->{'name'} ?? null,
                PhoneNumber: $Object->{'phone_number'} ?? null,
                Email: $Object->{'email'} ?? null,
                ShippingAddress: ShippingAddress::FromTelegramFormat($Object->{'shipping_address'} ?? null)
            );
        }

        return null;
    }
}

