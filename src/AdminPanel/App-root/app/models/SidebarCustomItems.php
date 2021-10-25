<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Models;

use ArrayIterator;

class SidebarCustomItems extends ArrayIterator
{
    public function __construct(SidebarCustomItem ...$Items)
    {
        parent::__construct($Items);
    }
    public function current(): SidebarCustomItem
    {
        return parent::current();
    }
    public function offsetGet($offset): SidebarCustomItem
    {
        return parent::offsetGet($offset);
    }

    /**
     * Метод для парсинга конфига из JSON
     * @param string $JSON JSON
     * @return array Конфиг
     */
    public static function ParseJSON(string $JSON): array
    {
        return json_decode($JSON, true);
    }

    /**
     * Метод для парсинга конфига из JSON файла
     * @param string $Filename Имя файла
     * @return array Конфиг
     */
    public static function FromJSONFile(string $Filename): self
    {
        $Parsed = self::ParseJSON(
            file_get_contents($Filename)
        );
        $ItemsArray = [];
        if(isset($Parsed['Items']))
        {
            foreach($Parsed['Items'] as $ArrayItem)
            {
                $Item = new SidebarCustomItem($ArrayItem);
                if($Item instanceof SidebarCustomItem)
                {
                    $ItemsArray[] = $Item;
                }
            }
        }


        $Items = new self(...$ItemsArray);

        return $Items;
    }
}