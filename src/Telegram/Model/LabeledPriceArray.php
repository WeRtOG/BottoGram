<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class LabeledPriceArray extends TelegramModelArray
{
    public function __construct(LabeledPrice ...$LabeledPrice)
    {
        parent::__construct($LabeledPrice);
    }

    public function current(): LabeledPrice
    {
        return parent::current();
    }

    public function offsetGet($Offset): LabeledPrice
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): LabeledPrice
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $LabeledPriceInTelegramFormat): ?self
    {
        $LabeledPriceArray = [];
        if($LabeledPriceInTelegramFormat != null)
        {
            foreach($LabeledPriceInTelegramFormat as $LabeledPrice)
            {
                $LabeledPriceArray[] = LabeledPrice::FromTelegramFormat($LabeledPrice);
            }

            return new self(...$LabeledPriceArray);
        }

        return null;
    }
}

