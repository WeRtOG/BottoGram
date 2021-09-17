<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Navigation;

use WeRtOG\BottoGram\BottoGram;
use WeRtOG\BottoGram\Models\TelegramUser;
use WeRtOG\BottoGram\Telegram\Model\Update;
use WeRtOG\BottoGram\Telegram\Telegram;

class Command
{
    public function __construct(
        public string $Name,
        public mixed $Action,
        public bool $ExitAfterExecute = true,
        public ?string $UpdateType = null
    )
    { }

    public function Execute(Update $Update, TelegramUser $User, Telegram $Telegram): bool
    {
        if(is_callable($this->Action))
        {
            call_user_func($this->Action, $Update, $User, $Telegram);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function __toString()
    {
        return $this->Name;
    }
}