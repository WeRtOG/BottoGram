<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ReflectionClass;
use ReflectionProperty;
use WeRtOG\BottoGram\Telegram\Telegram;

class ReplyKeyboardMarkup extends ReplyMarkup
{
    public function __construct(
        public array $Keyboard,
        public bool $ResizeKeyboard = false,
        public bool $OneTimeKeyboard = false,
        public bool $Selective = false
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

        foreach($PropsArray['Keyboard'] as &$Row)
        {
            foreach($Row as &$Button)
            {
                if($Button instanceof KeyboardButton)
                {
                    $Button = $Button->ToTelegramFormat($FilesOutput);
                }
            }
        }

        $Result = Telegram::ConvertToTelegramFormat($PropsArray, $FilesOutput);

        return json_encode($Result);
    }
}

