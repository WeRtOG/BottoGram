<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram;

use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseConnection;
use WeRtOG\BottoGram\Exceptions\BottoConfigException;
use WeRtOG\FoxyMVC\Exceptions\ModelException;
use WeRtOG\FoxyMVC\ModelHelper;

/**
 * Класс объекта конфига бота
 * @property string $Name Имя бота
 * @property string $Token Токен бота
 * @property array $DB Массив информации о БД
 * @property string $SessionUser Пользователь сессии
 * @property string $Logo Логотип
 * @property bool $Private Флаг приватности бота
 * @property array $PrivateAllow Список доверенных пользователей (для приватного режима)
 * @property bool $DarkTheme Флаг тёмной темы
 * @property string $Admin Контакт администратора
 * @property bool $ButtonsAutoSize Флаг автоматического размера кнопок
 * @property bool $AllowGroups Флаг разрешения групп
 * @property bool $EnableTextLog Флаг текстовых логов
 * @property string $ConfigFile Путь к файлу конфига
 */
class BottoConfig
{
    public string $Name;
    public string $Token;
    public DatabaseConnection $DatabaseConnection;
    public string $SessionUser = 'botto_user';
    public string $Logo = '';
    public bool $Private = false;
    public array $PrivateAllow = [];
    public bool $DarkTheme = false;
    public string $AdminContact = "@WeRtOG";
    public bool $ButtonsAutoSize = true;
    public bool $AllowGroups = true;
    public bool $EnableTextLog = true;
    public bool $EnableExtendedLog = false;
    public string $ConfigFile = '';

    /**
     * Конструктор класса объекта конфига бота
     * @param $Data Массив данных
     */
    public function __construct(array $Data)
    {
        try
        {
            ModelHelper::SetParametersFromArray($this, $Data, true, true);
        }
        catch(ModelException $Exception)
        {
            throw new BottoConfigException($Exception->getMessage());
        }
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
    public static function ParseJSONFile(string $Filename): array
    {
        $Config = self::ParseJSON(
            file_get_contents($Filename)
        );
        $Config['ConfigFile'] = $Filename;

        return $Config;
    }

    public static function CreateFromJSONFile(string $Filename): self
    {
        $ParsedData = self::ParseJSONFile($Filename);
        
        try
        {
            if(isset($ParsedData['DatabaseConnection']))
                $ParsedData['DatabaseConnection'] = new DatabaseConnection($ParsedData['DatabaseConnection']);
        }
        catch(ModelException $Exception)
        {
            throw new BottoConfigException($Exception->getMessage());
        }

        return new self($ParsedData);
    }

    /**
     * Метод для изменения параметра конфига
     * @param string $Name Название параметра
     * @param mixed $Value Значение параметра
     * @param string $ConfigFile Путь к файлу конфига
     * @return bool Результат (если 0 - файл не найден)
     */
    public static function ChangeParameter(string $Name, $Value, string $ConfigFile): bool
    {
        if(file_exists($ConfigFile))
        {
            $Config = self::ParseJSONFile($ConfigFile);
            $Config[$Name] = $Value;
            unset($Config['ConfigFile']);

            file_put_contents($ConfigFile, json_encode($Config, JSON_PRETTY_PRINT));

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Метод для изменения токена
     * @param string $Token Токен
     * @param string $ConfigFile Путь к файлу конфига
     * @return int Результат операции
     */
    public static function ChangeToken(string $Token, string $ConfigFile): int
    {
        return self::ChangeParameter('Token', $Token, $ConfigFile);
    }
    
}

?>