<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class InlineQueryResultArray extends TelegramModelArray
{
    public function __construct(InlineQueryResult ...$InlineQueryResult)
    {
        parent::__construct($InlineQueryResult);
    }

    public function current(): InlineQueryResult
    {
        return parent::current();
    }

    public function offsetGet($Offset): InlineQueryResult
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): InlineQueryResult
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }
}

