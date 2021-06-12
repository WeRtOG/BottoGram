<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Invoice
{
    public function __construct(
        public string $Title,
        public string $Description,
        public string $StartParameter,
        public string $Currency,
        public int $TotalAmount
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Title: $Object->{'title'},
                Description: $Object->{'description'},
                StartParameter: $Object->{'start_parameter'},
                Currency: $Object->{'currency'},
                TotalAmount: $Object->{'total_amount'}
            );
        }

        return null;
    }
}

?>