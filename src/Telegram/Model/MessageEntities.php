<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class MessageEntities extends ArrayIterator
{
    public function __construct(MessageEntity ...$MessageEntities)
    {
        parent::__construct($MessageEntities);
    }

    public function current(): MessageEntity
    {
        return parent::current();
    }

    public function offsetGet($Offset): MessageEntity
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): MessageEntity
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $MessageEntitiesInTelegramFormat): ?self
    {
        $MessageEntities = [];
        if($MessageEntitiesInTelegramFormat != null)
        {
            foreach($MessageEntitiesInTelegramFormat as $MessageEntity)
            {
                $MessageEntities[] = MessageEntity::FromTelegramFormat($MessageEntity);
            }

            return new self(...$MessageEntities);
        }

        return null;
    }
}

?>