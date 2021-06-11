<?php
/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

// Используем зависимости
use WeRtOG\BottoGram\DatabaseManager\Database;

/**
 * Модуль для работы с логами в Административной Панели
 * @property Database $Database База данных
 */
class Log
{
    protected Database $Database;

    public string $Table;

    /**
     * Конструктор класса для работы с логами
     * @param Database $Database База данных
     */
    function __construct(Database $Database)
    {
        $this->Database = $Database;
        $this->Table = BOTTOGRAM_DB_TABLE_BOTLOG;
    }

    /**
     * Метод для получения списка логов
     */
    public function GetLogs(): array
    {
        $Result = [];
        $QueryResult = $this->Database->FetchQuery("SELECT * FROM $this->Table ORDER BY ID DESC LIMIT 20", true);

        if($QueryResult != null)
        {
            foreach($QueryResult as $Item)
            {
                $Result[] = $Item;
            }
        }

        return $Result;
    }
}
?>