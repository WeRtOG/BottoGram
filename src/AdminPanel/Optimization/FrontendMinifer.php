<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Optimization;

use MatthiasMullie\Minify;

class FrontendMinifer
{
    public static function MinifyFromJSItem(MinifyItem $Item): void
    {
        $Minifier = new Minify\JS(...$Item->Source);
        $Minifier->minify($Item->Result);
    }

    public static function MinifyFromCSSItem(MinifyItem $Item): void
    {
        $Minifier = new Minify\CSS(...$Item->Source);
        $Minifier->minify($Item->Result);
    }

    public static function MinifyFromMap(MiniferMap $Map): array
    {
        $FilesToMinify = $Map->GetFilesToMinify();

        foreach($FilesToMinify['css'] as $Item)
        {
            self::MinifyFromCSSItem($Item);
        }

        foreach($FilesToMinify['js'] as $Item)
        {
            self::MinifyFromJSItem($Item);
        }

        return $FilesToMinify ?? [];
    }
}