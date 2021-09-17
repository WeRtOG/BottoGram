<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class Photo extends TelegramModelArray
{
    public function __construct(PhotoSize ...$Sizes)
    {
        parent::__construct($Sizes);
    }

    public function current(): PhotoSize
    {
        return parent::current();
    }

    public function offsetGet($Offset): PhotoSize
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): PhotoSize
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $SizesInTelegramFormat): ?self
    {
        $Sizes = [];
        if($SizesInTelegramFormat != null)
        {
            foreach($SizesInTelegramFormat as $Size)
            {
                $Sizes[] = PhotoSize::FromTelegramFormat($Size);
            }

            return new self(...$Sizes);
        }

        return null;
    }
}

