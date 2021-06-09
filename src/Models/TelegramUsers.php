<?php

    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Models;

    use Exception;
    use WeRtOG\BottoGram\DatabaseManager\Database;

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
            return $this->Database->FetchQuery("SELECT * FROM $this->Table WHERE ChatID='$ChatID' ORDER BY ID ASC LIMIT 1", false, TelegramUser::class);
        }

        /**
         * Метод для изменения последней медиагруппы
         * @param string $Group Медиагруппа
         * @param string $ChatID ID пользователя
         */
        public function SetUserLastMediaGroup(string $Group, string $ChatID)
        {
            $this->Database->FetchQuery("UPDATE $this->Table SET LastMediaGroup='$Group' WHERE ChatID='$ChatID'");
        }

        /**
         * Метод для получения последней медиагруппы пользователя
         * @param string|null $ChatID ID пользователя (Telegram)
         * @return string Медиагруппа
         */
        public function GetUserLastMediaGroup(?string $ChatID): string
        {
            $MediaGroup = '';
            $User = $this->GetUser($ChatID ?? '');

            if($User != null)
            {
                $MediaGroup = isset($User->LastMediaGroup) ? $User->LastMediaGroup : '';
            }

            return empty($MediaGroup) ? -1 : $MediaGroup;
        }

        /**
         * Метод для изменения навигации пользователя
         * @param string $Nav Навигация
         * @param string $ChatID ID Пользователя (Telegram)
         */
        public function SetUserNav(string $Nav, TelegramUser &$User)
        {
            $User->Nav = $Nav;
            return $this->Database->FetchQuery("UPDATE $this->Table SET Nav='$Nav' WHERE ChatID='$User->ChatID'");
        }

        /**
         * Метод для получения элемента кеша
         * @param string $Name Название элемента
         * 
         * @return mixed Значение
         */
        public function GetUserCacheItem(string $Name, TelegramUser &$User)
        {
            return $User->Cache[$Name] ?? null;
        }

        /**
         * Метод для изменения значения элемента кеша
         * @param string $Name Название элемента
         * @param $Value Значение элемента
         * 
         */
        public function SetUserCacheItem(string $Name, $Value, TelegramUser &$User)
        {
            $User->Cache = is_array($User->Cache) ? $User->Cache : [];
            $User->Cache[$Name] = $Value;
            $this->SetUserCache($User->Cache, $User);
        }

        /**
         * Метод для изменения кеша пользователя
         * @param mixed $Cache Кеш
         * @param string $ChatID ID пользователя (Telegram)
         */
        public function SetUserCache($Cache, TelegramUser $User)
        {
            $Cache = json_encode($Cache, JSON_UNESCAPED_UNICODE);   
            $this->Database->FetchQuery("UPDATE $this->Table SET Cache='$Cache' WHERE ChatID='$User->ChatID'");
        }
        
        /**
         * Метод для регистрации пользователя Telegram в БД
         * @param string $ChatID ID пользователя (Telegram)
         * @param string $UserName Имя пользователя
         * @param string $FullName Полное имя пользователя
         */
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

?>