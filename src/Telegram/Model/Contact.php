<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Contact
{
    public function __construct(
        public string $PhoneNumber,
        public string $FirstName,
        public ?string $LastName,
        public ?int $UserID,
        public ?string $VCard
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                PhoneNumber: $Object->{'phone_number'},
                FirstName: $Object->{'first_name'},
                LastName: $Object->{'last_name'} ?? null,
                UserID: $Object->{'user_id'} ?? null,
                VCard: $Object->{'vcard'} ?? null
            );
        }

        return null;
    }
}

?>