<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

// Используем зависимости
use WeRtOG\BottoGram\DatabaseManager\Database;


class AccessControl
{
    protected Database $Database;
    protected string $SessionKey;
    
    public AdminUsers $Users;

    function __construct(Database $Database, string $SessionKey)
    {
        session_start();

        $this->Database = $Database;
        $this->Users = new AdminUsers($Database);
        $this->SessionKey = $SessionKey;
    }

    public function IsAuthorized(): bool
    {
        return !empty($_SESSION[$this->SessionKey]);
    }

    public function GetUserName(): string
    {
        return $_SESSION[$this->SessionKey];
    }

    public function PrepareString(string $Source): string
    {
        return strip_tags($this->Database->EscapeString($Source));
    }

    public function DoLogin(string $Login, string $Password): int
    {
        $Login = $this->PrepareString($Login);
        $Password = $this->Users->MakeHash($Password);
    
        $UserByLogin = $this->Users->GetUserByLogin($Login);
        
        if($UserByLogin != null && $UserByLogin->Login == $Login)
        { 
            if($UserByLogin->Password == $Password)
            {
                $_SESSION[$this->SessionKey] = $Login;
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    public function DoLogout()
    {
        $_SESSION[$this->SessionKey] = "";
    }
}

?>