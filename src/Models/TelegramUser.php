<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Models;

use WeRtOG\FoxyMVC\Model;

class TelegramUser extends Model
{
    public int $ID = 1;
    public string $ChatID;
    public string $UserName;
    public string $FullName;
    public string $Nav;
    public ?array $Cache;
    public string $LastMediaGroup;

    public function __construct(array $Parameters = [])
    {
        if(isset($Parameters['Cache']) && !empty($Parameters['Cache']))
        {
            if(self::IsJSONString($Parameters['Cache']))
            {
                $Parameters['Cache'] = json_decode($Parameters['Cache'], true);
            }
        }

        parent::__construct($Parameters);
    }

    /**
     * Метод для проверки того, является ли строка валидным JSON
     * @param string|null $String Строка
     * @return bool Результат проверки
     */
    public static function IsJSONString(?string $String): bool
    {
        json_decode($String);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

?>