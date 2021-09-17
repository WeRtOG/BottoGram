<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class UsersArray extends TelegramModelArray
{
    public function __construct(User ...$Users)
    {
        parent::__construct($Users);
    }

    public function current(): User
    {
        return parent::current();
    }

    public function offsetGet($Offset): User
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): User
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $UsersInTelegramFormat): ?self
    {
        $Users = [];
        if($UsersInTelegramFormat != null)
        {
            foreach($UsersInTelegramFormat as $User)
            {
                $Users[] = User::FromTelegramFormat($User);
            }

            return new self(...$Users);
        }

        return null;
    }
}

