<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

use WeRtOG\BottoGram\DatabaseManager\Database;

class AdminUsers
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
        $this->Table = BOTTOGRAM_DB_TABLE_ADMIN_USERS;
    }

    /**
     * Метод для создания хеша
     * @param string $Source Исходная строка
     * @return string Хеш
     */
    public function MakeHash(string $Source): string
    {
        return strrev(sha1("NO".strrev(md5($Source."9R") . "jgi3g34g")));
    }

    /**
     * Метод для получения пользователя по логину
     * @param string $Login Логин
     * @return array|null Пользователь 
     */
    public function GetUserByLogin(?string $Login): ?AdminUser
    {
        return $this->Database->FetchQuery("SELECT * FROM $this->Table WHERE Login='$Login'", false, AdminUser::class);
    }

    public function GetUserByID(int $ID): ?AdminUser
    {
        return $this->Database->FetchQuery("SELECT * FROM $this->Table WHERE ID='$ID'", false, AdminUser::class);
    }

    /**
     * Метод для получения списка пользователей
     * @return array Список пользователей
     */
    public function GetUsers(): array
    {
        return $this->Database->FetchQuery("SELECT * FROM $this->Table", true, AdminUser::class) ?? [];
    }

    /**
     * Метод для проверки наличия пользователя
     * @param string $Login Логин
     * @return bool Результат проверки
     */
    public function CheckUserByLogin(string $Login): bool
    {
        return $this->GetUserByLogin($Login) != null;
    }

    public function CheckUserByID(string $ID): bool
    {
        return $this->GetUserByID($ID) != null;
    }

    /**
     * Метод для обновления пароля пользователя
     * @param string $ID ID
     * @param string $Password Пароль
     * @return int Результат операции
     */
    public function UpdateUserPassword(string $ID, string $Password): bool
    {
        $Password = $this->MakeHash($Password);
        
        if($this->CheckUserByID($ID))
        { 
            $this->Database->FetchQuery("UPDATE $this->Table SET Password='$Password' WHERE ID='$ID'");
            return true;
        }
        else
        {
            return false;
        }
    }

    public function UpdateUserRights(string $ID, bool $CanManageUsers = false, bool $CanChangeConfig = false, bool $CanViewRequestLogs = false): bool
    {
        if($this->CheckUserByID($ID))
        { 
            $this->Database->FetchQuery("UPDATE $this->Table SET CanManageUsers='" . (int)$CanManageUsers . "', CanChangeConfig='" . (int)$CanChangeConfig . "', CanViewRequestLogs='" . (int)$CanViewRequestLogs . "' WHERE ID='$ID'");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Метод для добавления пользователя
     * @param string $Login Логин
     * @param string $Password Пароль
     * @param string $CanManageUsers Флаг возможности управления пользователями
     * @param string $CanChangeConfig Флаг возможности редактирования конфигурации
     * @param string $CanViewRequestLogs Флаг возможности просмотра логов
     * @return bool Резултат операции
     */
    public function AddUser(string $Login, string $Password, bool $CanManageUsers = false, bool $CanChangeConfig = false, bool $CanViewRequestLogs = false): bool
    {
        if(!$this->CheckUserByLogin($Login))
        {
            $Login = $this->Database->EscapeString($Login);
            $Password = $this->MakeHash($Password); 
            $this->Database->FetchQuery("INSERT INTO $this->Table (Login, Password, CanManageUsers, CanChangeConfig, CanViewRequestLogs) VALUES ('$Login', '$Password', '" . (int)$CanManageUsers . "', '" . (int)$CanChangeConfig . "', '" . (int)$CanViewRequestLogs . "')");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Метод для удаления пользователя по логину
     * @param string $ID Логин
     * @return int Резултат операции
     */
    public function DeleteUser(int $ID): bool
    {
        if($this->CheckUserByID($ID))
        {
            $this->Database->FetchQuery("DELETE FROM $this->Table WHERE ID='$ID'");
            $this->Database->FetchQuery("ALTER TABLE $this->Table AUTO_INCREMENT = 1");
            return true;
        }
        else
        {
            return false;
        }
    }

}