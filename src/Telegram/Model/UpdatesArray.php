<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class UpdatesArray extends TelegramModelArray
{
    public function __construct(Update ...$Updates)
    {
        parent::__construct($Updates);
    }

    public function current(): Update
    {
        return parent::current();
    }

    public function offsetGet($Offset): Update
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): Update
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $UpdatesInTelegramFormat): ?self
    {
        $Updates = [];
        if($UpdatesInTelegramFormat != null)
        {
            foreach($UpdatesInTelegramFormat as $Update)
            {
                $Updates[] = Update::FromTelegramFormat($Update);
            }

            return new self(...$Updates);
        }

        return null;
    }
}

