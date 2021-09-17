<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class InputMediaArray extends TelegramModelArray
{
    public function __construct(InputMedia ...$InputMedia)
    {
        parent::__construct($InputMedia);
    }

    public function current(): InputMedia
    {
        return parent::current();
    }

    public function offsetGet($Offset): InputMedia
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): InputMedia
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }
}

