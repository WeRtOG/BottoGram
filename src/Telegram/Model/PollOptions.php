<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class PollOptions extends ArrayIterator
{
    public function __construct(PollOption ...$PollOptions)
    {
        parent::__construct($PollOptions);
    }

    public function current(): PollOption
    {
        return parent::current();
    }

    public function offsetGet($Offset): PollOption
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): PollOption
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $PollOptionsInTelegramFormat): ?self
    {
        $PollOptions = [];
        if($PollOptionsInTelegramFormat != null)
        {
            foreach($PollOptionsInTelegramFormat as $PollOption)
            {
                $PollOptions[] = PollOption::FromTelegramFormat($PollOption);
            }

            return new self(...$PollOptions);
        }

        return null;
    }
}

?>