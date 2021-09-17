<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class BotCommandArray extends TelegramModelArray
{
    public function __construct(BotCommand ...$BotCommand)
    {
        parent::__construct($BotCommand);
    }

    public function current(): BotCommand
    {
        return parent::current();
    }

    public function offsetGet($Offset): BotCommand
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): BotCommand
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }
}

