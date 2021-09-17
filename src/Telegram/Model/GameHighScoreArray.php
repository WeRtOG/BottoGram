<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class GameHighScoreArray extends TelegramModelArray
{
    public function __construct(GameHighScore ...$GameHighScore)
    {
        parent::__construct($GameHighScore);
    }

    public function current(): GameHighScore
    {
        return parent::current();
    }

    public function offsetGet($Offset): GameHighScore
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): GameHighScore
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }
}

