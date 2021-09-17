<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class EncryptedPassportElement extends TelegramModel
{
    public function __construct(
        public string $Type,
        public string $Hash,
        public ?string $Data,
        public ?string $PhoneNumber,
        public ?string $Email,
        public ?PassportFiles $Files,
        public ?PassportFile $FrontSide,
        public ?PassportFile $ReverseSide,
        public ?PassportFile $Selfie,
        public ?PassportFiles $Translation,
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Type: $Object->{'type'},
                Hash: $Object->{'hash'},
                Data: $Object->{'data'} ?? null,
                PhoneNumber: $Object->{'phone_number'} ?? null,
                Email: $Object->{'email'} ?? null,
                Files: PassportFiles::FromTelegramFormat($Object->{'files'} ?? null),
                FrontSide: PassportFile::FromTelegramFormat($Object->{'front_side'} ?? null),
                ReverseSide: PassportFile::FromTelegramFormat($Object->{'reverse_side'} ?? null),
                Selfie: PassportFile::FromTelegramFormat($Object->{'selfie'} ?? null),
                Translation: PassportFiles::FromTelegramFormat($Object->{'translation'} ?? null)
            );
        }

        return null;
    }
}

