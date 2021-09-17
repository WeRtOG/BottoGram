<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class ShippingOptionArray extends TelegramModelArray
{
    public function __construct(ShippingOption ...$ShippingOptionArray)
    {
        parent::__construct($ShippingOptionArray);
    }

    public function current(): ShippingOption
    {
        return parent::current();
    }

    public function offsetGet($Offset): ShippingOption
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): ShippingOption
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $ShippingOptionInTelegramFormat): ?self
    {
        $ShippingOptionArray = [];
        if($ShippingOptionInTelegramFormat != null)
        {
            foreach($ShippingOptionInTelegramFormat as $ShippingOption)
            {
                $ShippingOptionArray[] = ShippingOption::FromTelegramFormat($ShippingOption);
            }

            return new self(...$ShippingOptionArray);
        }

        return null;
    }
}

