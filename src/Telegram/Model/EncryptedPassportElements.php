<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class EncryptedPassportElements extends TelegramModelArray
{
    public function __construct(EncryptedPassportElement ...$EncryptedPassportElements)
    {
        parent::__construct($EncryptedPassportElements);
    }

    public function current(): EncryptedPassportElement
    {
        return parent::current();
    }

    public function offsetGet($Offset): EncryptedPassportElement
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): EncryptedPassportElement
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $EncryptedPassportElementsInTelegramFormat): ?self
    {
        $EncryptedPassportElements = [];
        if($EncryptedPassportElementsInTelegramFormat != null)
        {
            foreach($EncryptedPassportElementsInTelegramFormat as $EncryptedPassportElement)
            {
                $EncryptedPassportElements[] = EncryptedPassportElement::FromTelegramFormat($EncryptedPassportElement);
            }

            return new self(...$EncryptedPassportElements);
        }

        return null;
    }
}

