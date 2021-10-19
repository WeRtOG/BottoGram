<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

use WeRtOG\BottoGram\DatabaseManager\Database;

class Analytics
{
    protected Database $Database;

    private string $BotUsersTable;
    private string $BotLogTable;

    public function __construct(Database $Database)
    {
        $this->Database = $Database;
        $this->BotUsersTable = BOTTOGRAM_DB_TABLE_BOTUSERS;
        $this->BotLogTable = BOTTOGRAM_DB_TABLE_BOTLOG;
    }

    private function SQLCount(string $Query): int
    {
        $Result = $this->Database->FetchQuery($Query);
        return $Result != null ? $Result['Count'] : 0;
    }

    public function GetUsersCount(): int
    {
        return $this->SQLCount("SELECT COUNT(*) AS Count FROM $this->BotUsersTable");
    }

    public function GetRequestsCount(): int
    {
        return $this->SQLCount("SELECT COUNT(*) AS Count FROM $this->BotLogTable");
    }

    public function GetDailyUsersCount(): int
    {
        return $this->SQLCount("SELECT COUNT(DISTINCT $this->BotLogTable.ChatID) AS Count FROM $this->BotLogTable WHERE DATE(Date) = CURDATE()");
    }

    public function GetDailyRequestsCount(): int
    {
        return $this->SQLCount("SELECT COUNT(*) AS Count FROM $this->BotLogTable WHERE DATE(Date) = CURDATE()");
    }

    public function GetWeeklyUsersCount(): int
    {
        return $this->SQLCount("SELECT COUNT(DISTINCT $this->BotLogTable.ChatID) AS Count FROM $this->BotLogTable WHERE YEARWEEK(DATE($this->BotLogTable.Date), 1) = YEARWEEK(CURDATE(), 1)");
    }

    public function GetWeeklyRequestsCount(): int
    {
        return $this->SQLCount("SELECT COUNT(*) AS Count FROM $this->BotLogTable WHERE YEARWEEK(DATE($this->BotLogTable.Date), 1) = YEARWEEK(CURDATE(), 1)");
    }

    public function GetWeeklyUsersGraph(): array
    {
        $Graph = [
            'Labels' => [],
            'Data' => [
                'Users' => [],
                'Requests' => []
            ]
        ];
        $Result = $this->Database->FetchQuery("SELECT DATE($this->BotLogTable.Date) AS 'Date', COUNT(DISTINCT($this->BotLogTable.ChatID)) AS 'Count', COUNT($this->BotLogTable.ChatID) AS 'CountQuery' FROM $this->BotLogTable GROUP BY DATE($this->BotLogTable.Date) ORDER BY DATE($this->BotLogTable.Date) DESC LIMIT 7", true);
        
        if($Result != null)
        {
            $Result = array_reverse($Result);

            foreach($Result as $Row)
            {
                $Time = strtotime($Row['Date']);

                $Graph['Labels'][] = date('d.m.Y', $Time);
                $Graph['Data']['Users'][] = (int)$Row['Count'];
                $Graph['Data']['Requests'][] = (int)$Row['CountQuery'];
            }
        }
        
        return $Graph;
    }

    public function GetDayUsersGraph(): array
    {
        $Graph = [
            'Labels' => [],
            'Data' => [
                'Users' => [],
                'Requests' => []
            ]
        ];

        $Result = $this->Database->FetchQuery(
            "SELECT HOUR(bl.Date) AS 'Hours', COUNT(DISTINCT(bl.ChatID)) AS 'Count', COUNT(bl.ChatID) AS 'CountQuery'
            FROM $this->BotLogTable AS bl
            WHERE DATE(bl.Date) = DATE(NOW())
            GROUP BY HOUR(bl.Date)"
            , true
        );
        if($Result != null)
        {
            foreach($Result as $Row)
            {
                $Graph['Labels'][] = $Row['Hours'] . ':00';
                $Graph['Data']['Users'][] = (int)$Row['Count'];
                $Graph['Data']['Requests'][] = (int)$Row['CountQuery'];
            }
        }

        return $Graph;
    }

    public function GetNewUsersGraph(): array
    {
        $UsersCount = $this->GetUsersCount();
        
        $Graph = [
            'Labels' => [],
            'Data' => []
        ];

        $Result = $this->Database->FetchQuery(
            "SELECT bu.RegistrationDate, COUNT(DISTINCT(bu.ChatID)) AS Count
            FROM $this->BotUsersTable AS bu
            GROUP BY DATE(bu.RegistrationDate)
            ORDER BY DATE(bu.RegistrationDate) DESC LIMIT 7"
            , true
        );
        if($Result != null)
        {
            foreach($Result as $Index => $Row)
            {
                $Time = strtotime($Row['RegistrationDate']);
                $NewCount = $Index == 0 ? $UsersCount : $UsersCount - $Result[$Index - 1]['Count'];

                $Graph['Labels'][] = date('d.m.Y', $Time);
                $Graph['Data']['Dynamic'][] = (int)$NewCount;
                $Graph['Data']['Count'][] = (int)$Row['Count'];

                $UsersCount = $NewCount;
            }
        }

        $Graph['Labels'] = array_reverse($Graph['Labels']);
        $Graph['Data']['Dynamic'] = array_reverse($Graph['Data']['Dynamic']);
        $Graph['Data']['Count'] = array_reverse($Graph['Data']['Count']);

        return $Graph;
    }

    public function GetTimezone(): string
    {
        $Result = $this->Database->FetchQuery('SELECT EXTRACT(HOUR FROM (TIMEDIFF(NOW(), UTC_TIMESTAMP))) AS Timezone', false) ?? ['Timezone' => 0];
        return $Result >= 0 ? '+' . $Result['Timezone'] : $Result['Timezone'];
    }
}