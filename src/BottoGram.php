<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram;

    include 'Constants.php';
    
    // Используем зависимости
    use Exception;
    use WeRtOG\BottoGram\BottoConfig;
    use WeRtOG\BottoGram\DatabaseManager\Database;
    use WeRtOG\BottoGram\DatabaseManager\DatabaseManager;
    use WeRtOG\BottoGram\Log;
    use WeRtOG\BottoGram\Models\MenuFolder;
    use WeRtOG\BottoGram\Telegram\Telegram;
    use WeRtOG\BottoGram\Telegram\Model\Message;
    use WeRtOG\BottoGram\Telegram\Model\Response as TelegramResponse;
    use WeRtOG\BottoGram\Navigation\Menu;
    use WeRtOG\BottoGram\Navigation\Command;
    use WeRtOG\BottoGram\Navigation\KeyboardState;
    use WeRtOG\BottoGram\Telegram\Model\ChatAction;
    use WeRtOG\BottoGram\Telegram\Model\ParseMode;
    use WeRtOG\BottoGram\Models\TelegramUser;
    use WeRtOG\BottoGram\Models\TelegramUsers;
    use WeRtOG\BottoGram\Telegram\Model\Update;
    use WeRtOG\BottoGram\Telegram\Model\UpdateType;

    /**
     * # BottoGram
     * ##### By WeRtOG
     */
    class BottoGram
    {
        private array $CustomModels = [];
        private array $MenuFoldersList = [];
        private array $Commands = [];
        private int $OldMediaGroup = 0;
        private int $NewMediaGroup = 0;

        public Database $Database;

        public TelegramUsers $TelegramUsers;
        public Telegram $Telegram;
        public BottoConfig $Config;
        
        public ?Log $Log = null;
        public ?TelegramUser $CurrentUser = null;
        public ?Update $Update;

        public $InlineQueryAction;
        public $PreCheckoutQueryAction;

        public string $RootMenu;

        public array|string|null $Keyboard = null;

        /**
         * Конструктор класса
         * @param BottoConfig $Config Конфиг бота
         * @param bool $DoLogic Выполнять ли логику
         */
        function __construct(BottoConfig $Config, bool $DoLogic = true)
        {
            $this->Config = $Config;

            // Инициализируем класс для работы с Telegram
            $this->Telegram = new Telegram($Config->Token, $Config->ButtonsAutoSize);

            // Подключаемся к БД
            $this->Database = self::DatabaseFromConfig($Config);

            $this->TelegramUsers = new TelegramUsers($this->Database);

            // Получаем и запоминаем основные сведения о боте

            if($DoLogic)
            {
                $this->Update = $this->Telegram->GetUpdate();

                switch($this->Update->Type)
                {
                    case UpdateType::Message:
                        if(!$this->Update->Message->IsChannelPost)
                        {
                            // Регистрируем Telegram юзера в БД (если он не зарегистрирован)
                            $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists($this->Update->Message->ChatID, $this->Update->Message->UserName, $this->Update->Message->UserFullName);
        
                            // Отладка
                            $this->Log = new Log($this->Update->Message->ChatID, $this->Update->Request, $this->Database, $Config->EnableTextLog, $Config->EnableExtendedLog);
        
                            if($this->Update->Message->Text == BOT_COMMAND_GETID)
                            {
                                $this->Log->RequestSuccess();
                                $this->Send("👤 Твой ID: " . $this->Update->Message->ChatID);
                                exit();
                            }
        
                            if($this->Update->Message->IsFromGroup && !$this->Config->AllowGroups)
                            {
                                $this->Log->RequestFail(403, "Groups not allowed.");
                                exit();
                            }
                
                            if($Config->Private && !in_array($this->Update->Message->ChatID, $Config->PrivateAllow))
                            {
                                $this->Log->RequestFail(403, "User not allowed.");
                                $this->Send("🚫 Доступ к данному боту ограничен.");
                                exit();
                            }
                
                            if($this->Update->Message->MessageID == -1 && $this->Update->Type == UpdateType::Message)
                            {
                                // Отображаем юзеру, что бот что-то пишет
                                $this->SendChatAction(ChatAction::Typing);
                            }
                
                            // Получаем и запоминаем последнюю медиагруппу
                            $this->OldMediaGroup = $this->TelegramUsers->GetUserLastMediaGroup($this->Update->Message->ChatID);
                            
                            // Если сообщение принадлежит к медиагруппе, то сохраняем её и запоминаем в БД
                            if($this->Update->Message->IsMediaGroup)
                            {
                                $this->NewMediaGroup = $this->Update->Message->MediaGroupID;
                                $this->TelegramUsers->SetUserLastMediaGroup($this->Update->Message->MediaGroupID, $this->Update->Message->ChatID);
                            }
                        }
                        else
                        {
                            // Регистрируем Telegram юзера в БД (если он не зарегистрирован)
                            $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists($this->Update->Message->ChatID, 'Channel: ' . $this->Update->Message->ChatID, '');
    
                            // Отладка
                            $this->Log = new Log($this->Update->Message->ChatID, $this->Update->Request, $this->Database, $Config->EnableTextLog, $Config->EnableExtendedLog);
    
                            $Text = $this->Update->Message->Data->{'channel_post'}->{'text'} ?? '';
                            if($Text == BOT_COMMAND_GETID)
                            {
                                $Response = $this->Telegram->SendMessage("👤 Channel ID: " . $this->Update->Message->ChatID, $this->Update->Message->ChatID);
                                $this->Log->ResponseSuccess($Response);
                            }
    
                            $this->Log->RequestSuccess();
                        }
                        break;

                    case UpdateType::InlineQuery:
                        $this->Log = new Log($this->Update->InlineQuery->ChatID, $this->Update->Request, $this->Database, $Config->EnableTextLog, $Config->EnableExtendedLog);

                        $this->Update->Message = new Message(
                            ChatID: $this->Update->InlineQuery->ChatID
                        );
                        break;

                    case UpdateType::PreCheckoutQuery:
                        $this->Log = new Log($this->Update->PreCheckoutQuery->ChatID, $this->Update->Request, $this->Database, $Config->EnableTextLog, $Config->EnableExtendedLog);
    
                        $this->Update->Message = new Message(
                            ChatID: $this->Update->PreCheckoutQuery->ChatID
                        );
                        break;

                }
            }
            else
            {
                $this->Log = new Log(-1, $this->Update->Request, $this->Database, $Config->EnableTextLog, $Config->EnableExtendedLog);
            }
        }

        /**
         * Метод для генерации БД из конфига
         * @param BottoConfig Конфиг
         * @return Database|null БД
         */
        public static function DatabaseFromConfig(BottoConfig $Config): ?Database
        {
            return DatabaseManager::Connect($Config->DatabaseConnection);
        }

        /**
         * Метод для получения нужной модели по типу класса из массива моделей
         * @param string $Class Имя класса
         * @param array $Models
         * @return mixed|null Нужная модель либо null
         */
        public static function GetModel(string $Class, array $Models)
        {
            foreach($Models as $Model)
            {
                if($Model instanceof $Class)
                {
                    return $Model;
                }
            }
            return null;
        }

        /**
         * Метод для подключения всех меню из папки
         * @param string $Folder Папка
         * @param mixed $Parameters Параметры
         */
        public function ConnectMenuFolder(string $Folder, string $Namespace = '', ...$Models)
        {
            $Nav = $this->CurrentUser->Nav ?? '';

            $this->LastMenuFolderPath = $Folder;
            $this->CustomModels = array_merge($this->CustomModels, $Models);

            if(empty($Nav)) $Nav = $this->RootMenu;

            if(file_exists($Folder))
                $this->MenuFoldersList[] = new MenuFolder(Path: $Folder, Namespace: $Namespace);
        }

        /**
         * Функция для проверки наличия новой медиагруппы
         * @return bool Результат проверки
         */
        public function HasNewMediaGroup(): bool
        {
            return ($this->OldMediaGroup != $this->NewMediaGroup) && $this->OldMediaGroup != 0 && $this->NewMediaGroup != 0;
        }

        /**
         * Метод для задания корневого меню
         * @param string $menu Навигационное название меню
         */
        public function SetRootMenu(string $menu)
        {
            $this->RootMenu = $menu;
        }

        public function PrepareKeyboard(string|array $Keyboard): string|array|null
        {
            switch($Keyboard)
            {
                case KeyboardState::KeepLastKeyboard:
                    $Keyboard = $this->Keyboard;
                    break;
                
                case KeyboardState::WithoutChanges:
                    $Keyboard = null;
                    break;

                case KeyboardState::RemoveKeyboard:
                default:
                    $this->Keyboard = $Keyboard;
                    break;
            }

            return $Keyboard;
        }

        /**
         * Метод для отправки сообщения
         * @param string $Text текст сообщения
         * @param bool $RemoveLastKeyboard флаг удаления последней клавиатуры
         * @param array $MainKeyboard Массив кнопок основной клавиатуры
         * @param array $InlineKeyboard Массив кнопок инлайновой клавиатуры
         * @param string $Channel ID канала (необязательно)
         * @param string $ParseMode Метод парсинга
         * @return TelegramResponse Результат операции
         */
        public function Send(string $Text, string|array $MainKeyboard = KeyboardState::KeepLastKeyboard, ?array $InlineKeyboard = [], string $Channel = "", string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $MainKeyboard = $this->PrepareKeyboard($MainKeyboard);
                
                $Response = $this->Telegram->SendMessage($Text, $this->Update->Message->ChatID, $MainKeyboard, $InlineKeyboard, $ParseMode);
            }
            else
            {
                if(empty($InlineKeyboard)) $InlineKeyboard = null;
                $Response = $this->Telegram->SendMessage($Text, $Channel, null, $InlineKeyboard, $ParseMode);
            }

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для отправки фотографии
         * @param string $Photo Картинка
         * @param string $Text Текст
         * @param bool $RemoveLastKeyboard Флаг удаления последней клавиатуры
         * @param array $MainKeyboard Основная клавиатура
         * @param array $InlineKeyboard Инлайновая клавиатура
         * @return TelegramResponse Результат операции
         */
        public function SendPhoto(string $Photo, string $Text = "", ?array $MainKeyboard = [], string $Channel = "", ?array $InlineKeyboard = []): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $MainKeyboard = $this->PrepareKeyboard($MainKeyboard);

                if(empty($InlineKeyboard))
                {
                    $Response = $this->Telegram->SendPhoto($Photo, $this->Update->Message->ChatID, $Text, $MainKeyboard, []);
                }
                else
                {
                    $Response = $this->Telegram->SendPhoto($Photo, $this->Update->Message->ChatID, $Text, null, $InlineKeyboard);
                }
                
            }
            else
            {
                $Response = $this->Telegram->SendPhoto($Photo, $Channel);
            }
            
            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для отправки видео
         * @param string $Video Видео
         * @param string $Text Текст
         * @param bool $RemoveLastKeyboard Флаг удаления последней клавиатуры
         * @param array $MainKeyboard Основная клавиатура
         * @param array $InlineKeyboard Инлайновая клавиатура
         * @param string $ParseMode Метод парсинга
         * @return TelegramResponse Результат операции
         */
        public function SendVideo(string $Video, string $Text = "", ?array $MainKeyboard = [], string $Channel = "", ?array $InlineKeyboard = [], string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $MainKeyboard = $this->PrepareKeyboard($MainKeyboard);

                if(empty($InlineKeyboard))
                {
                    $Response = $this->Telegram->SendVideo($Video, $this->Update->Message->ChatID, $Text, $MainKeyboard, [], $ParseMode);
                }
                else
                {
                    $Response = $this->Telegram->SendVideo($Video, $this->Update->Message->ChatID, $Text, null, $InlineKeyboard, $ParseMode);
                }
                
            }
            else
            {
                $Response = $this->Telegram->SendVideo($Video, $Channel, $Text, null, $InlineKeyboard, $ParseMode);
            }
            
            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Альтернативный метод для отправки фотографии
         * @param string $Photo Картинка
         * @param string $Text Текст
         * @param bool $RemoveLastKeyboard Флаг удаления последней клавиатуры
         * @param array $MainKeyboard Основная клавиатура
         * @param array $InlineKeyboard Инлайновая клавиатура
         * @return TelegramResponse Результат операции
         */
        public function SendPhotoAlt(string $Photo, string $Text = "", ?array $MainKeyboard = [], string $Channel = "", ?array $InlineKeyboard = []): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $MainKeyboard = $this->PrepareKeyboard($MainKeyboard);
                
                if(empty($InlineKeyboard))
                {
                    $Response = $this->Telegram->SendPhotoAlt($Photo, $this->Update->Message->ChatID, $Text, $MainKeyboard, []);
                }
                else
                {
                    $Response = $this->Telegram->SendPhotoAlt($Photo, $this->Update->Message->ChatID, $Text, null, $InlineKeyboard);
                }
                
            }
            else
            {
                $Response = $this->Telegram->SendPhotoAlt($Photo, $Channel);
            }
            
            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для отправки документа
         * @param string $document Документ
         * @return TelegramResponse Результат операции
         */
        public function SendDocument(string $document): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->SendDocument($document, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);

            return $Response;
        }

        /**
         * Метод для отправки геолокации
         * @param $lat Широта
         * @param $long Долгота
         * @return TelegramResponse Результат операции
         */
        public function SendLocation(string $lat, string $long): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->SendLocation($lat, $long, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для пересылки сообщения
         * @param int $MessageID ID сообщения
         * @param string $ChatID ID чата
         * @return TelegramResponse Результат операции
         */
        public function ForwardMessage(int $MessageID, string $ChatID): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->ForwardMessage($ChatID, $MessageID, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для редактирования сообщения
         * @param string $MessageID ID сообщения
         * @param string $NewText Новый текст
         * @param string $ParseMode Метод парсинга
         * @return TelegramResponse Результат операции
         */
        public function EditMessage(string $MessageID, string $NewText, string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->EditMessage($MessageID, $NewText, $this->Update->Message->ChatID, $ParseMode);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для редактирования инлайновых кнопок сообщения
         * @param int $MessageID ID сообщения
         * @param array $InlineKeyboard Инлайновая клавиатура
         * @return TelegramResponse Результат операции
         */
        public function EditMessageInlineButtons(int $MessageID, array $InlineKeyboard): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->EditMessageInlineButtons($MessageID, $InlineKeyboard, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для удаления сообщения
         * @param int $MessageID ID сообщения
         * @return TelegramResponse Результат операции
         */
        public function DeleteMessage(int $MessageID): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->DeleteMessage($MessageID, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }


        public function SendChatAction(string $Action): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->SendChatAction($Action, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для скачивания файла с серверов Telegram
         * @param string $ID ID файла
         * @param string $Folder Путь к папке, в которую нужно разместить файл (необязательно)
         * @return string Имя конечного файла
         */
        public function GetFileFromID(string $ID, string $Folder = 'uploads'): string
        {
            return $this->Telegram->GetFile($this->Telegram->GetFilename($ID), $Folder);
        }

        /**
         * Метод для получения пути к файлу на серверах Telegram
         * @param string $ID ID файла
         * @return string Путь к файлу на серверах Telegram
         */
        public function GetFilenameFromID(string $ID): string
        {
            return $this->Telegram->GetFilename($ID);
        }

        /**
         * Метод для получения файла по пути с серверов Telegram
         * @param string $Path Путь к файлу
         * @return string Путь к загруженному файлу
         */
        public function GetFileFromPath(string $Path): string
        {
            return $this->Telegram->GetFile($Path);
        }

        /**
         * Метод для получения BLOB файла с серверов Telegram
         * @param int $ID ID файла
         * @return string BLOB
         */
        public function GetBlobFromID(int $ID): string
        {
            return $this->Telegram->GetBlob($this->Telegram->GetFilename($ID));
        }

        /**
         * Метод для отправки группы фотографий
         * @param array $Photos Фотографии
         * @param string $Caption Подпись
         * @param string $Channel ID канала
         * @param bool $isID Является ли фото ID
         * @param string $ParseMode Метод парсинга
         * @return TelegramResponse Результат операции
         */
        public function SendPhotoGroup(array $Photos, string $Caption = "", string $Channel = "", bool $isID = false, string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $Channel = $this->Update->Message->ChatID;
            }

            $Response = $this->Telegram->SendPhotoGroup($Photos, $Channel, $Caption, $isID, $ParseMode);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для отправки медиагруппы
         * @param array $Content Контент
         * @param string $Caption Подпись
         * @param string $Channel ID канала
         * @param string $ParseMode Метод парсинга
         * @return TelegramResponse Результат операции
         */
        public function SendMediaGroup(array $Content, string $Caption = "", string $Channel = "", string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();
            
            if(empty($Channel))
            {
                $Channel = $this->Update->Message->ChatID;
            }

            $Response = $this->Telegram->SendMediaGroup($Content, $Channel, $Caption, $ParseMode);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        private function GetMenuFromClassNameIfExists(string $ClassName): ?Menu
        {
            if(class_exists($ClassName))
            {
                $Menu = new $ClassName;
                if($Menu instanceof Menu)
                {
                    return $Menu;
                }
            }

            return null;
        }

        /**
         * Метод для получения объекта меню по имени
         * @param string $name Имя меню
         * @return Menu Меню
         */
        private function GetMenuByName(string $Name): ?Menu
        {
            $Menu = null;

            foreach($this->MenuFoldersList as $MenuFolder)
            {
                if($MenuFolder instanceof MenuFolder)
                {
                    $ClassName = $MenuFolder->Namespace . '\\' . $Name;
                    $ClassFileName = $MenuFolder->Path . '/' . $Name . '.php';

                    $Menu = $this->GetMenuFromClassNameIfExists($ClassName);

                    if($Menu == null)
                    {
                        if(file_exists($ClassFileName))
                        {
                            include $ClassFileName;
                            $Menu = $this->GetMenuFromClassNameIfExists($ClassName);
                        }
                    }
                    else
                    {
                        break;
                    }
                }
            }

            return $Menu;
        }

        /**
         * Метод для получения действия кнопки меню по сообщению (только если кнопка затронута)
         * @param string $Text Текст сообщения
         * @param Menu $Menu Меню
         * @return callable Действие
         */
        private function GetKeyboardActionFromMessage(string $Text, Menu $Menu): ?callable
        {
            if(is_array($Menu->Buttons))
            {
                foreach($Menu->Buttons as $Row)
                {
                    foreach($Row as $Button)
                    {
                        if($Button->Title == $Text) {
                            return $Button->Action;
                        }
                    }
                }
            }
            return null;
        }

        /**
         * Метод для перезагрузки меню
         * @param bool $Silent Не выполнять ли действие OnLoad
         * @return void
         */
        public function ReloadMenu(bool $Silent = false): void
        {
            $this->NavTo($this->CurrentUser->Nav, $Silent);
        }

        /**
         * Метод для навигации в нужное меню
         * @param string $Nav Имя меню
         * @param bool $Silent Не выполнять ли действие OnLoad
         */
        public function NavTo(string $Nav, bool $Silent = false)
        {
            $this->TelegramUsers->SetUserNav($Nav, $this->CurrentUser);
            $this->TelegramUsers->SetUserLastMediaGroup("-1", $this->Update->Message->ChatID);

            $CurrentMenu = $this->GetMenuByName($Nav);
            
            if(method_exists($CurrentMenu, 'OnInit'))
                if($CurrentMenu != null) $CurrentMenu->{'OnInit'}($this, $this->CustomModels);

            if($Silent) return;

            $this->Update->Message->Text = BOT_COMMAND_INIT;
            $this->Update->Message->Command = BOT_COMMAND_INIT;
            $this->Keyboard = $CurrentMenu->Buttons ?? null;

            
            $Action = isset($CurrentMenu->OnLoad) ? $CurrentMenu->OnLoad : null;

            if(is_callable($Action))
            {
                call_user_func($Action, $this->Update->Message, $this);
            }
            else
            {
                if(method_exists($CurrentMenu, 'OnMessage'))
                {
                    $CurrentMenu->{'OnMessage'}($this->Update->Message, $this);
                }
            }
        }

        /**
         * Метод для изменения кеша
         * @param $Cache Кеш
         */
        public function SetCache($Cache): void
        {
            $this->TelegramUsers->SetUserCache($Cache, $this->CurrentUser);
            $this->CurrentUser = $this->TelegramUsers->GetUser($this->CurrentUser->ChatID);
        }

        /**
         * Метод для изменения значения элемента кеша
         * @param string $Name Название элемента
         * @param $Value Значение элемента
         */
        public function SetCacheItem(string $Name, $Value): void
        {
            $this->TelegramUsers->SetUserCacheItem($Name, $Value, $this->CurrentUser);
            $this->CurrentUser = $this->TelegramUsers->GetUser($this->CurrentUser->ChatID);
        }

        /**
         * Метод для получения кеша
         * @return mixed Кеш
         */
        public function GetCache()
        {
            return $this->CurrentUser->Cache ?? null;
        }

        /**
         * Метод для получения элемента кеша
         * @param string $Name Название элемента
         * @return mixed Значение
         */
        public function GetCacheItem(string $Name)
        {
            return $this->TelegramUsers->GetUserCacheItem($Name, $this->CurrentUser);
        }

        /**
         * Метод для навигации в Главное Меню
         * @param bool $Silent Не выполнять ли действие OnLoad
         */
        public function NavToRoot(bool $Silent = false)
        {
            $this->NavTo($this->RootMenu, $Silent);
        }

        /**
         * Метод для обработки ошибки
         * @param string $Message Сообщение ошибки
         * @param bool $PHPError Является ли ошибка ошибкой PHP
         */
        public function OnError(string $Message, bool $PHPError = true)
        {
            $this->Log->RequestFail(500, $Message);
            
            if($PHPError)
            {
                $this->Send("🐘 <b>Ошибка PHP:</b> \n\n" . $Message, [], [], '', 'html');
                exit();
            }
            else 
            {
                $this->Send("⛔️ *Ошибка:* \n\n" . $Message);
            }    
        }

        /**
         * Метод для получения ссылки на корневую директорию бота
         * @param string Ссылка
         */
        public function GetURL(): string
        {
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https:' : 'http:';
            $dir = dirname($_SERVER['SCRIPT_NAME']);
            return $protocol . '//' . $_SERVER['HTTP_HOST'] . $dir . '/';
        }

        /**
         * Задание действия для обработки события появления инлайнового запроса
         * @param callable $Action Действие
         */
        public function OnInlineQuery($Action)
        {
            $this->Update->InlineQueryAction = $Action;
        }

        /**
         * Задание действия для обработки события появления запроса на подтверждение оплаты
         * @param callable $Action Действие
         */
        public function OnPreCheckoutQuery($Action)
        {
            $this->Update->PreCheckoutQueryAction = $Action;
        }

        /**
         * Метод для получения навигации
         * @return string Навигация
         */
        public function GetNav(): ?string
        {
            return $this->CurrentUser->Nav ?? null;
        }

        /**
         * Метод для валидации номера телефона
         * @param string $Phone Номер телефона
         * @param string $CountryValidCode Правильный код страны
         * @return bool Результат валидации
         */
        public function PhoneIsValid(string $Phone, string $CountryValidCode = '380'): bool
        {
            $Phone = str_replace('+', '', $Phone);
            return !empty($Phone) && preg_match("/[0-9]{10}$/", $Phone) && substr($Phone, 0, 3) == "380" && strlen($Phone) == 12;
        }

        public function RegisterCommand(Command $Command): void
        {
            $this->Commands[] = $Command;
        }

        public function AnswerCallbackQuery(Message $Message, bool $AutoDeleteMessage = true, string $NotificationText = null, bool $ShowAlert = false): TelegramResponse
        {
            $Response = $this->Telegram->AnswerCallbackQuery($Message->CallbackQueryID, $NotificationText, $ShowAlert);
            if($Message->Command != BOT_COMMAND_CALLBACK_NODELETE)
            {
                $this->DeleteMessage($Message->MessageID);
            }

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        /**
         * Метод для инициализации движка
         */
        public function Init()
        {
            switch($this->Update->Type)
            {
                case UpdateType::Message:
                    if(!$this->Update->Message->IsChannelPost)
                    {
                        if($this->Update->Message->Command == BOT_COMMAND_RESET || $this->Update->Message->Command == BOT_COMMAND_START)
                        {
                            $this->TelegramUsers->SetUserNav($this->RootMenu, $this->CurrentUser);
                        }
        
                        $Nav = &$this->CurrentUser->Nav;
                        if(empty($Nav))
                            $Nav = $this->RootMenu;
        
                        $this->TelegramUsers->SetUserNav($Nav, $this->CurrentUser);
                        
                        $CurrentMenu = $this->GetMenuByName($Nav);
                        if($CurrentMenu != null)
                        {
                            if(method_exists($CurrentMenu, 'OnInit'))
                                $CurrentMenu->{'OnInit'}($this, $this->CustomModels);
                            
                            $KeyboardAction = $this->GetKeyboardActionFromMessage($this->Update->Message->Text, $CurrentMenu);
                            $this->Keyboard = $CurrentMenu->Buttons ?? null;
        
                            if($this->Update->Message->IsCallbackQuery)
                            {
                                if(method_exists($CurrentMenu, 'OnCallbackQuery'))
                                {
                                    $CurrentMenu->{'OnCallbackQuery'}($this->Update->Message, $this);
                                }
                            }
        
                            $ExecuteMenuOrKeyboardAction = true;
                            foreach($this->Commands as $Command)
                            {
                                if($Command instanceof Command)
                                {
                                    if($this->Update->Message->Command == $Command->Name)
                                    {
                                        $Command->Execute($this->Update, $this);
            
                                        if($Command->ExitAfterExecute) {
                                            $this->Log->RequestSuccess();
                                            $ExecuteMenuOrKeyboardAction = false;
                                        }
                                    }
                                }
                            }
        
                            if($this->Update->Message->IsPay)
                            {
                                if(method_exists($CurrentMenu, 'OnPay')) $CurrentMenu->{'OnPay'}($this->Update->Message, $this);
                            }
                            else
                            {
                                if($ExecuteMenuOrKeyboardAction)
                                {
                                    if($KeyboardAction != null)
                                    {
                                        if(is_callable($KeyboardAction))
                                        {
                                            call_user_func($KeyboardAction, $this->Update->Message, $this);
                                        }
                                    }
                                    else
                                    {
                                        if(method_exists($CurrentMenu, 'OnMessage'))
                                        {
                                            $CurrentMenu->{'OnMessage'}($this->Update->Message, $this);
                                        }
                                    }
                                }
                            }
                        }
                        else
                        {
                            $this->Send("*Меню не найдено. 😞*\nДля того, чтобы вернутся в главное меню введите " . BOT_COMMAND_START);
                        }
                    }
                    break;
                
                case UpdateType::InlineQuery:
                    if(is_callable($this->Update->InlineQueryAction))
                    { 
                        call_user_func($this->Update->InlineQueryAction, $this->Update->InlineQuery, $this);       
                    }
                    break;

                case UpdateType::PreCheckoutQuery:
                    if(is_callable($this->Update->PreCheckoutQueryAction))
                    { 
                        call_user_func($this->Update->PreCheckoutQueryAction, $this->Update->PreCheckoutQuery, $this);       
                    }
                    break;
            }                

            $this->Log->RequestSuccess();
        }
    }
?>