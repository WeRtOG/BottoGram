<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class StickerArray extends TelegramModelArray
{
    public function __construct(Sticker ...$Stickers)
    {
        parent::__construct($Stickers);
    }

    public function current(): Sticker
    {
        return parent::current();
    }

    public function offsetGet($Offset): Sticker
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): Sticker
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $StickersInTelegramFormat): ?self
    {
        $Stickers = [];
        if($StickersInTelegramFormat != null)
        {
            foreach($StickersInTelegramFormat as $Sticker)
            {
                $Stickers[] = Sticker::FromTelegramFormat($Sticker);
            }

            return new self(...$Stickers);
        }

        return null;
    }
}

