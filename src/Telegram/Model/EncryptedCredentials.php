<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class EncryptedCredentials
{
    public function __construct(
        public string $Data,
        public string $Hash,
        public string $Secret
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Data: $Object->{'data'},
                Hash: $Object->{'hash'},
                Secret: $Object->{'secret'}
            );
        }

        return null;
    }
}

?>