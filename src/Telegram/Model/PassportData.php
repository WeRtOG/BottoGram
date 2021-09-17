<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class PassportData extends TelegramModel
{
    public function __construct(
        public EncryptedPassportElements $Data,
        public EncryptedCredentials $Credentials
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Data: EncryptedPassportElements::FromTelegramFormat($Object->{'data'}),
                Credentials: EncryptedCredentials::FromTelegramFormat($Object->{'credentials'})
            );
        }

        return null;
    }
}

