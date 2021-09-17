<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Navigation;

use WeRtOG\BottoGram\Telegram\Model\KeyboardButton;
use WeRtOG\BottoGram\Telegram\Model\KeyboardButtonPollType;

class Button extends KeyboardButton
{
    public function __construct(
        public string $Text,
        public mixed $Action = null,
        public bool $RequestContact = false,
        public bool $RequestLocation = false,
        public ?KeyboardButtonPollType $RequestPoll = null
    )
    { }
}