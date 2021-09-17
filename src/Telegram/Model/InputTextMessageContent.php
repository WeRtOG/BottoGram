<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputTextMessageContent extends InputMessageContent
{
    public function __construct(
        public string $MessageText,
        public ?string $ParseMode = null,
        public ?MessageEntities $Entities = null,
        public bool $DisableWebPagePreview = false
    )
    { }
}

