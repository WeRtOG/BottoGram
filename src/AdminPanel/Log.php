<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

use WeRtOG\BottoGram\DatabaseManager\Database;

class Log
{
    protected Database $Database;

    public string $Table;

    function __construct(Database $Database)
    {
        $this->Database = $Database;
        $this->Table = BOTTOGRAM_DB_TABLE_BOTLOG;
    }

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