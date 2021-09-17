<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class StickerSetArray extends TelegramModelArray
{
    public function __construct(StickerSet ...$StickerSetArray)
    {
        parent::__construct($StickerSetArray);
    }

    public function current(): StickerSet
    {
        return parent::current();
    }

    public function offsetGet($Offset): StickerSet
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): StickerSet
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $StickerSetInTelegramFormat): ?self
    {
        $StickerSetArray = [];
        if($StickerSetInTelegramFormat != null)
        {
            foreach($StickerSetInTelegramFormat as $StickerSet)
            {
                $StickerSetArray[] = StickerSet::FromTelegramFormat($StickerSet);
            }

            return new self(...$StickerSetArray);
        }

        return null;
    }
}

