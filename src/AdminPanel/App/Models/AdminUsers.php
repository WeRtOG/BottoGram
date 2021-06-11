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
    public function GetUserByLogin(string $Login): ?AdminUser
    {
        return $this->Database->FetchQuery("SELECT * FROM $this->Table WHERE Login='$Login'", false, AdminUser::class);
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

    /**
     * Метод для обновления пароля пользователя
     * @param string $Login Логин
     * @param string $Password Пароль
     * @return int Результат операции
     */
    public function UpdatePassword(string $Login, string $Password): bool
    {
        $Password = $this->MakeHash($Password);
        
        if($this->CheckUserByLogin($Login))
        { 
            $this->Database->FetchQuery("UPDATE $this->Table SET Password='$Password' WHERE Login='$Login'");
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
     * @return int Резултат операции
     */
    public function AddUser(string $Login, string $Password): int
    {
        if(!$this->CheckUserByLogin($Login))
        {
            $Password = $this->MakeHash($Password); 
            $this->Database->FetchQuery("INSERT INTO $this->Table (Login, Password) VALUES ('$Login', '$Password')");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Метод для удаления пользователя по логину
     * @param string $Login Логин
     * @return int Резултат операции
     */
    public function DeleteUser(string $Login): bool
    {
        if($this->CheckUserByLogin($Login))
        {
            $this->Database->FetchQuery("DELETE FROM $this->Table WHERE Login='$Login'");
            $this->Database->FetchQuery("ALTER TABLE $this->Table AUTO_INCREMENT = 1");
            return true;
        }
        else
        {
            return false;
        }
    }

}

?>