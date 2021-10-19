<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

use WeRtOG\BottoGram\DatabaseManager\Database;

class RequestLogs
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
        return $this->Database->FetchQuery("SELECT bottogram_log.*, bottogram_users.ID as UserID FROM bottogram_log JOIN bottogram_users ON bottogram_log.ChatID = bottogram_users.ChatID ORDER BY ID DESC LIMIT 20", true, RequestLog::class) ?? [];
    }
}