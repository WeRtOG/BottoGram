<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputContactMessageContent extends InputMessageContent
{
    public function __construct(
        public string $PhoneNumber,
        public string $FirstName,
        public ?string $LastName,
        public ?string $VCard
    )
    { }
}

