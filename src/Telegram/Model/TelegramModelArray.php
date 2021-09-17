<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;
use ReflectionClass;
use ReflectionProperty;
use WeRtOG\BottoGram\Telegram\Telegram;

class TelegramModelArray extends ArrayIterator
{
    public function __construct(array $TelegramModelArray)
    {
        parent::__construct($TelegramModelArray);
    }

    public function current(): TelegramModel
    {
        return parent::current();
    }

    public function offsetGet($Offset): TelegramModel
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): TelegramModel
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public function ToTelegramFormat(?array &$FilesOutput = null): string
    {
        $Result = Telegram::ConvertToTelegramFormat((array)$this, $FilesOutput);
        return json_encode($Result);
    }

}

