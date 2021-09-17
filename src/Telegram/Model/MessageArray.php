<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class MessageArray extends TelegramModelArray
{
    public function __construct(Message ...$MessageArray)
    {
        parent::__construct($MessageArray);
    }

    public function current(): Message
    {
        return parent::current();
    }

    public function offsetGet($Offset): Message
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): Message
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $MessageInTelegramFormat): ?self
    {
        $MessageArray = [];
        if($MessageInTelegramFormat != null)
        {
            foreach($MessageInTelegramFormat as $Message)
            {
                $MessageArray[] = Message::FromTelegramFormat($Message);
            }

            return new self(...$MessageArray);
        }

        return null;
    }
}

