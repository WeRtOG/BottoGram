<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class PassportElementErrorArray extends TelegramModelArray
{
    public function __construct(PassportElementError ...$PassportElementErrorArray)
    {
        parent::__construct($PassportElementErrorArray);
    }

    public function current(): PassportElementError
    {
        return parent::current();
    }

    public function offsetGet($Offset): PassportElementError
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): PassportElementError
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $PassportElementErrorInTelegramFormat): ?self
    {
        $PassportElementErrorArray = [];
        if($PassportElementErrorInTelegramFormat != null)
        {
            foreach($PassportElementErrorInTelegramFormat as $PassportElementError)
            {
                $PassportElementErrorArray[] = PassportElementError::FromTelegramFormat($PassportElementError);
            }

            return new self(...$PassportElementErrorArray);
        }

        return null;
    }
}

