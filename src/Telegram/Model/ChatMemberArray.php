<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class ChatMemberArray extends TelegramModelArray
{
    public function __construct(ChatMember ...$ChatMemberArray)
    {
        parent::__construct($ChatMemberArray);
    }

    public function current(): ChatMember
    {
        return parent::current();
    }

    public function offsetGet($Offset): ChatMember
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): ChatMember
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $ChatMemberInTelegramFormat): ?self
    {
        $ChatMemberArray = [];
        if($ChatMemberInTelegramFormat != null)
        {
            foreach($ChatMemberInTelegramFormat as $ChatMember)
            {
                $ChatMemberArray[] = ChatMember::FromTelegramFormat($ChatMember);
            }

            return new self(...$ChatMemberArray);
        }

        return null;
    }
}

