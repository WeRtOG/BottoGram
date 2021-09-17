<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use WeRtOG\BottoGram\Telegram\Telegram;

class TelegramModel
{
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

        $Result = Telegram::ConvertToTelegramFormat($PropsArray, $FilesOutput);

        return (object)$Result;
    }
}