<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\DatabaseManager;

use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseConnection;
use WeRtOG\BottoGram\DatabaseManager\Exceptions\DatabaseException;

/**
 * Класс-менеджер баз данных 
 * @property array $Connections Список подключений
 */
class DatabaseManager
{
    private static array $Connections = [];
    
    /**
     * Метод для подключения к БД
     * 
     * @return Database БД
     */
    public static function Connect(DatabaseConnection $DatabaseConnection): Database
    {
        $Database = new Database($DatabaseConnection);

        self::$Connections[] = $Database;

        /*
        $DB->CheckTable("bottogram_users");
        $DB->CheckTable("bottogram_log");
        $DB->CheckTable("users");*/

        return $Database;
    }

    /**
     * Метод для получения списка подключений
     * @return array Список подключений
     */
    public static function GetConnections(): array
    {
        return self::$Connections;
    }

    /**
     * Метод для закрытия всех подключений
     * @return bool Результат операции
     */
    public static function CloseAll(): bool
    {
        if(count(self::$Connections) > 0)
        {
            foreach(self::$Connections as $Connection)
            {
                $Connection->Disconnect();
            }

            self::$Connections = [];
            
            return true;
        }
        else
        {
            return false;
        }
    }
}