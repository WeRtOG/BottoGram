<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\DatabaseManager;

foreach (glob(__DIR__ . "/Exceptions/*.php") as $Filename) require_once $Filename;
foreach (glob(__DIR__ . "/Models/*.php") as $Filename) require_once $Filename;

use mysqli;
use WeRtOG\BottoGram\DatabaseManager\Exceptions\DatabaseException;
use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseConnection;

class Database
{
    protected object $DB;
    protected DatabaseConnection $DatabaseConnection;

    public function __construct(DatabaseConnection $DatabaseConnection)
    {
        $this->DatabaseConnection = $DatabaseConnection;

        $this->Connect();
    }

    public function Connect()
    {
        $this->DB = @new mysqli(
            $this->DatabaseConnection->Server,
            $this->DatabaseConnection->User,
            $this->DatabaseConnection->Password,
            $this->DatabaseConnection->Database
        );

        if(mysqli_connect_errno())
        {
            throw new DatabaseException("Подключение к серверу MySQL невозможно. Причина: " . mysqli_connect_error());
        }

        $this->DB->set_charset("utf8mb4");
    }

    public function Reconnect()
    {
        $this->Disconnect();
        $this->Connect();
    }

    public function Disconnect()
    {
        $this->DB->close();
    }

    public function FetchQuery(string $Query, bool $ReturnArray = false, string $ClassName = null)
    {
        if(!$this->DB->ping()) $this->Reconnect();

        while($this->DB->next_result()) $this->DB->store_result();

        $QueryResult = $this->DB->query($Query);

        if($QueryResult)
        {

            if(array_key_exists('num_rows', (array)$QueryResult))
            {
                if($QueryResult->num_rows > 0)
                {
                    $FetchResult = $QueryResult->fetch_assoc();

                    if($QueryResult->num_rows == 1 && $ReturnArray == false)
                    {
                        if($ClassName != null && class_exists($ClassName))
                        {
                            return new $ClassName($FetchResult);
                        }

                        else 
                        {
                            return $FetchResult;
                        }

                    } else if($QueryResult->num_rows >= 1) {

                        $Result = [];

                        do {

                            if($ClassName != null && class_exists($ClassName))
                            {
                                $Result[] = new $ClassName($FetchResult);
                            }

                            else 
                            {
                                $Result[] = $FetchResult;
                            }
                        }
                        while($FetchResult = $QueryResult->fetch_assoc());
                        
                        return $Result;
                    }
                } else {
                    return null;
                }
            } else {
                return null;
            }
        
        } else {
            throw new DatabaseException("Возникла ошибка MySQL: " . $this->DB->error . "\n при выполнении запроса: " . $Query);
        }
    }
    
    /**
     * Метод для получения ID последней вставленной строки
     * @return mixed ID строки
     */
    public function GetInsertID()
    {
        return $this->DB->insert_id;
    }

    /**
     * Метод для вызова хранимой процедуры
     * @param string $Name Имя процедуры
     * @param array $Parameters Массив значений параметров
     * @param bool $ReturnArray Флаг для принудительного возвращения массива
     * @param string $ClassName Имя класса 
     * @return mixed Результат
     */
    public function CallProcedure(string $Name, array $Parameters = [], bool $ReturnArray = false, string $ClassName = null)
    {
        $ParametersString = count($Parameters) > 0 ? "'" . implode("', '", $Parameters) . "'" : "";
        $ParametersString = str_replace("'NULL'", 'NULL', $ParametersString);
        return $this->FetchQuery("CALL $Name($ParametersString)", $ReturnArray, $ClassName);
    }

    /**
     * Метод для вызова хранимой функции
     * @param string $Name Имя процедуры
     * @param array $Parameters Массив значений параметров
     * @param string $ClassName Имя класса 
     * @return mixed Результат
     */
    public function CallFunction(string $Name, array $Parameters = [], string $ClassName = null) {
        $ParametersString = count($Parameters) > 0 ? "'" . implode("', '", $Parameters) . "'" : "";
        $ParametersString = str_replace("'NULL'", 'NULL', $ParametersString);
        return $this->FetchQuery("SELECT $Name($ParametersString) AS $Name", $ClassName)[$Name];
    }

    /**
     * Метод для получения безопасной строки (защита от SQL-инъекций)
     * @param string $String Строка
     * @return string Безопасная строка
     */
    public function EscapeString(string $String) : string {
        return $this->DB->real_escape_string($String);
    }

    /**
     * Метод для проверки существования таблицы и создания её (при отсутствии)
     * @param string $Name Имя таблицы
     * @param bool $CreateTable флаг создания таблицы при её отсутствии
     * @return bool Результат проверки
     */
    public function CheckTable(string $Name, bool $CreateTable = true) : bool
    {
        $query = $this->DB->query("SHOW TABLES LIKE '$Name';");
        $count = isset($query->num_rows) ? ($query->num_rows >= 1 ? $query->num_rows : 0) : 0;

        if($count == 0)
        {
            $sql_path = __DIR__ . '\\default\\database\\' . $Name.'.sql';

            if(file_exists($sql_path) && $CreateTable)
            {
                $commands = file_get_contents($sql_path);   
                $this->DB->multi_query($commands);
                while($this->DB->next_result()) $this->DB->store_result();
            }

            return false;
        }

        return true;
    }
}