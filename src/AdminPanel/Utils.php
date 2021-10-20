<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

class Utils
{
    public static function NumWord(float $Value, array $Words, bool $Show = true): string
    {
        $Num = $Value % 100;
        if ($Num > 19) { 
            $Num = $Num % 10; 
        }
        
        $Out = ($Show) ?  $Value . ' ' : '';
        switch ($Num) {
            case 1:  $Out .= $Words[0]; break;
            case 2: 
            case 3: 
            case 4:  $Out .= $Words[1]; break;
            default: $Out .= $Words[2]; break;
        }
        
        return $Out;
    }
}