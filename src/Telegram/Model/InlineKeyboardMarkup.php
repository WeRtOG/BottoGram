<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ReflectionClass;
use ReflectionProperty;
use WeRtOG\BottoGram\Telegram\Telegram;

class InlineKeyboardMarkup extends ReplyMarkup
{
    public function __construct(
        public array $InlineKeyboard
    )
    { }

    public function ToTelegramFormat(?array &$FilesOutput = null): mixed
    {
        $PropsArray = [];

        $Reflect = new ReflectionClass($this);
        $Props = $Reflect->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($Props as $Prop)
        {
            if($Prop instanceof ReflectionProperty)
            {
                $Name = $Prop->getName();
                $Value = $Prop->getValue($this);
                
                $PropsArray[$Name] = $Value;
            }
        }

        foreach($PropsArray['InlineKeyboard'] as &$Row)
        {
            foreach($Row as &$Button)
            {
                if($Button instanceof InlineKeyboardButton)
                {
                    $Button = $Button->ToTelegramFormat($FilesOutput);
                }
            }
        }

        $Result = Telegram::ConvertToTelegramFormat($PropsArray, $FilesOutput);

        return $Result;
    }
}

