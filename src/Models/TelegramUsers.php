<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Models;

use Exception;
use WeRtOG\BottoGram\DatabaseManager\Database;
use WeRtOG\BottoGram\DatabaseManager\Models\ChangedProperty;

class TelegramUsers
{
    protected Database $Database;
    
    private string $Table;

    /**
     * Метод конструктора класса модели эпизодов
     * @param Database $Database БД
     */
    public function __construct(Database $Database)
    {
        $this->Database = $Database;
        $this->Table = BOTTOGRAM_DB_TABLE_BOTUSERS;
    }

    public function GetUser(string $ChatID): ?TelegramUser
    {
        $User = $this->Database->FetchQuery("SELECT * FROM $this->Table WHERE ChatID='$ChatID' ORDER BY ID ASC LIMIT 1", false, TelegramUser::class);
        if($User != null)
        {
            $User->OnPropertyChange(function (ChangedProperty $Property) use ($User) {
                if(is_array($Property->Value))
                    $Property->Value = json_encode($Property->Value, JSON_UNESCAPED_UNICODE);
                
                $this->Database->FetchQuery("UPDATE $this->Table SET $Property->Name='$Property->Value' WHERE ID='$User->ID'");
                echo PHP_EOL . 'Property ' . $Property->Name . ' changed to "' . $Property->Value . '"' . PHP_EOL . PHP_EOL;
            });
        }

        return $User;
    }

    public function GetAllUsers(int $Page, int $Limit = 30): array
    {
        $Offset = ($Page - 1) * $Limit;
        return $this->Database->FetchQuery("SELECT * FROM $this->Table ORDER BY ID ASC LIMIT $Offset, $Limit", true, TelegramUser::class) ?? [];
    }

    public function GetAllUsersPagesCount(int $Limit = 30): int
    {
        $DatabaseResult = $this->Database->FetchQuery("SELECT CEIL(COUNT(*) / $Limit) AS Count FROM $this->Table ORDER BY ID DESC", false);
        return $DatabaseResult['Count'] ?? 0;
    }

    public function RegisterUserIfNotExists(string $ChatID, ?string $UserName, ?string $FullName): TelegramUser
    {
        $User = $this->GetUser($ChatID);

        if($User == null)
        {
            $this->Database->FetchQuery("INSERT INTO $this->Table (ChatID, UserName, FullName) VALUES ('$ChatID', '$UserName', '$FullName')");
            
            $User = $this->GetUser($ChatID);
            if($User != null)
            {
                return $User;
            }
            else
            {
                throw new Exception("DatabaseError: Telegram user creation failed.");
            }
        }
        else
        {
            if(isset($User->UserName) && $User->UserName != $UserName || isset($User->FullName) && $User->FullName != $FullName)
            {
                $this->Database->FetchQuery("UPDATE $this->Table SET UserName='$UserName', FullName='$FullName' WHERE ChatID='$ChatID'");
            }

            return $User;
        }
    }
}