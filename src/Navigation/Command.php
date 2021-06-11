<?php
/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Navigation;

use WeRtOG\BottoGram\BottoGram;
use WeRtOG\BottoGram\Telegram\Model\Update;

class Command
{
    public function __construct(
        public string $Name,
        public mixed $Action,
        public bool $ExitAfterExecute = true
    )
    { }

    public function Execute(Update $Update, BottoGram $BottoGram): bool
    {
        if(is_callable($this->Action))
        {
            call_user_func($this->Action, $Update, $BottoGram);
            return true;
        }
        else
        {
            return false;
        }
    }

    public function __toString()
    {
        return $this->Name;
    }
}
?>