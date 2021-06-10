<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram;

    $AutoloadFile = __DIR__ . '/../vendor/autoload.php';
    if(file_exists($AutoloadFile)) require_once $AutoloadFile;
    
    require_once 'Constants.php';
    
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
    use WeRtOG\BottoGram\Telegram\Model\MessageType;
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


        function __construct(BottoConfig $Config, bool $GetInputUpdate = true)
        {
            $this->Config = $Config;

            $this->Telegram = new Telegram(
                Token: $Config->Token,
                ButtonsAutoSize: $Config->ButtonsAutoSize
            );

            $this->Database = self::DatabaseFromConfig($Config);
            $this->TelegramUsers = new TelegramUsers($this->Database);

            if($GetInputUpdate)
            {
                $this->Update = $this->Telegram->GetUpdate();

                switch($this->Update->Type)
                {
                    case UpdateType::Message:
                        if(!$this->Update->Message->IsChannelPost)
                        {
                            $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                                ChatID: $this->Update->Message->ChatID,
                                UserName: $this->Update->Message->UserName,
                                FullName: $this->Update->Message->UserFullName
                            );

                            $this->Log = new Log(
                                ChatID: $this->Update->Message->ChatID,
                                Request: $this->Update->Request,
                                Database: $this->Database,
                                EnableTextLog: $Config->EnableTextLog,
                                EnableExtendedLog: $Config->EnableExtendedLog
                            );
        
                            if($this->Update->Message->Text == BOT_COMMAND_GETID)
                            {
                                $this->Log->RequestSuccess();
                                $this->Send("👤 Your ID: " . $this->Update->Message->ChatID);
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
                                $this->Send("🚫 Access to this bot is restricted.");
                                exit();
                            }
                
                            if($this->Update->Message->MessageID == -1)
                            {
                                $this->SendChatAction(ChatAction::Typing);
                            }
                
                            $this->OldMediaGroup = $this->TelegramUsers->GetUserLastMediaGroup($this->Update->Message->ChatID);
                            
                            if($this->Update->Message->Type == MessageType::MediaGroup)
                            {
                                $this->NewMediaGroup = $this->Update->Message->MediaGroupID;
                                $this->TelegramUsers->SetUserLastMediaGroup(
                                    Group: $this->Update->Message->MediaGroupID,
                                    ChatID: $this->Update->Message->ChatID
                                );
                            }
                        }
                        else
                        {
                            $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                                ChatID: $this->Update->Message->ChatID,
                                UserName: 'Channel: ' . $this->Update->Message->ChatID,
                                FullName: ''
                            );
                            $this->Log = new Log(
                                ChatID: $this->Update->Message->ChatID,
                                Request: $this->Update->Request,
                                Database: $this->Database,
                                EnableTextLog: $Config->EnableTextLog,
                                EnableExtendedLog: $Config->EnableExtendedLog);
    
                            $Text = $this->Update->Message->Data->{'channel_post'}->{'text'} ?? '';
                            if($Text == BOT_COMMAND_GETID)
                            {
                                $Response = $this->Telegram->SendMessage("👤 Channel ID: " . $this->Update->Message->ChatID, $this->Update->Message->ChatID);
                                $this->Log->ResponseSuccess($Response->GetData());
                            }
    
                            $this->Log->RequestSuccess();
                        }
                        break;

                    case UpdateType::InlineQuery:
                        $this->Log = new Log(
                            ChatID: $this->Update->InlineQuery->ChatID,
                            Request: $this->Update->Request,
                            Database: $this->Database,
                            EnableTextLog: $Config->EnableTextLog,
                            EnableExtendedLog: $Config->EnableExtendedLog
                        );

                        $this->Update->Message = new Message(
                            ChatID: $this->Update->InlineQuery->ChatID
                        );
                        break;

                    case UpdateType::PreCheckoutQuery:
                        $this->Log = new Log(
                            ChatID: $this->Update->PreCheckoutQuery->ChatID,
                            Request: $this->Update->Request,
                            Database: $this->Database,
                            EnableTextLog: $Config->EnableTextLog,
                            EnableExtendedLog: $Config->EnableExtendedLog
                        );
    
                        $this->Update->Message = new Message(
                            ChatID: $this->Update->PreCheckoutQuery->ChatID
                        );
                        break;

                }
            }
            else
            {
                $this->Log = new Log(
                    ChatID: -1,
                    Request: $this->Update->Request,
                    Database: $this->Database,
                    EnableTextLog: $Config->EnableTextLog,
                    EnableExtendedLog: $Config->EnableExtendedLog
                );
            }
        }

        public static function DatabaseFromConfig(BottoConfig $Config): ?Database
        {
            return DatabaseManager::Connect($Config->DatabaseConnection);
        }

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
        public function ConnectMenuFolder(string $Folder, string $Namespace = '', ...$Models)
        {
            $this->LastMenuFolderPath = $Folder;
            $this->CustomModels = array_merge($this->CustomModels, $Models);

            if(file_exists($Folder))
                $this->MenuFoldersList[] = new MenuFolder(Path: $Folder, Namespace: $Namespace);
        }

        public function HasNewMediaGroup(): bool
        {
            return ($this->OldMediaGroup != $this->NewMediaGroup) && $this->OldMediaGroup != 0 && $this->NewMediaGroup != 0;
        }

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

        public function SendPhotoByURL(string $Photo, string $Text = "", ?array $MainKeyboard = [], string $Channel = "", ?array $InlineKeyboard = []): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            if(empty($Channel))
            {
                $MainKeyboard = $this->PrepareKeyboard($MainKeyboard);

                if(empty($InlineKeyboard))
                {
                    $Response = $this->Telegram->SendPhotoByURL($Photo, $this->Update->Message->ChatID, $Text, $MainKeyboard, []);
                }
                else
                {
                    $Response = $this->Telegram->SendPhotoByURL($Photo, $this->Update->Message->ChatID, $Text, null, $InlineKeyboard);
                }
                
            }
            else
            {
                $Response = $this->Telegram->SendPhotoByURL($Photo, $Channel);
            }
            
            $this->Log->ProcessResponse($Response);
            return $Response;
        }

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

        public function SendPhoto(string $Photo, string $Text = "", string|array $MainKeyboard = KeyboardState::KeepLastKeyboard, string $Channel = "", array|null $InlineKeyboard = []): TelegramResponse
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

        public function SendDocument(string $document): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->SendDocument($document, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);

            return $Response;
        }

        public function SendLocation(string $lat, string $long): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->SendLocation($lat, $long, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        public function ForwardMessage(int $MessageID, string $ChatID): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->ForwardMessage($ChatID, $MessageID, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        public function EditMessage(string $MessageID, string $NewText, string $ParseMode = ParseMode::Markdown): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->EditMessage($MessageID, $NewText, $this->Update->Message->ChatID, $ParseMode);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

        public function EditMessageInlineButtons(int $MessageID, array $InlineKeyboard): TelegramResponse
        {
            if($this->Update->Message == null) return new TelegramResponse();

            $Response = $this->Telegram->EditMessageInlineButtons($MessageID, $InlineKeyboard, $this->Update->Message->ChatID);

            $this->Log->ProcessResponse($Response);
            return $Response;
        }

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

        public function GetFileFromID(string $ID, string $Folder = 'uploads'): string
        {
            return $this->Telegram->GetFile($this->Telegram->GetFilename($ID), $Folder);
        }

        public function GetFilenameFromID(string $ID): string
        {
            return $this->Telegram->GetFilename($ID);
        }

        public function GetFileFromPath(string $Path): string
        {
            return $this->Telegram->GetFile($Path);
        }

        public function GetBlobFromID(int $ID): string
        {
            return $this->Telegram->GetBlob($this->Telegram->GetFilename($ID));
        }

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

        public function ReloadMenu(bool $Silent = false): void
        {
            $this->NavTo($this->CurrentUser->Nav, $Silent);
        }

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

        public function SetCache($Cache): void
        {
            $this->TelegramUsers->SetUserCache($Cache, $this->CurrentUser);
            $this->CurrentUser = $this->TelegramUsers->GetUser($this->CurrentUser->ChatID);
        }

        public function SetCacheItem(string $Name, $Value): void
        {
            $this->TelegramUsers->SetUserCacheItem($Name, $Value, $this->CurrentUser);
            $this->CurrentUser = $this->TelegramUsers->GetUser($this->CurrentUser->ChatID);
        }

        public function GetCache()
        {
            return $this->CurrentUser->Cache ?? null;
        }

        public function GetCacheItem(string $Name)
        {
            return $this->TelegramUsers->GetUserCacheItem($Name, $this->CurrentUser);
        }

        public function NavToRoot(bool $Silent = false)
        {
            $this->NavTo($this->RootMenu, $Silent);
        }

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

        public function GetURL(): string
        {
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https:' : 'http:';
            $dir = dirname($_SERVER['SCRIPT_NAME']);
            return $protocol . '//' . $_SERVER['HTTP_HOST'] . $dir . '/';
        }

        public function OnInlineQuery($Action)
        {
            $this->Update->InlineQueryAction = $Action;
        }

        public function OnPreCheckoutQuery($Action)
        {
            $this->Update->PreCheckoutQueryAction = $Action;
        }

        public function GetNav(): ?string
        {
            return $this->CurrentUser->Nav ?? null;
        }


        public function PhoneIsValid(string $Phone, string $CountryValidCode = '380'): bool
        {
            $Phone = str_replace('+', '', $Phone);
            return !empty($Phone) && preg_match("/[0-9]{10}$/", $Phone) && substr($Phone, 0, 3) == $CountryValidCode && strlen($Phone) >= 12;
        }

        public function RegisterCommand(Command $Command): void
        {
            $this->Commands[] = $Command;
        }

        public function AnswerCallbackQuery(Message $Message, bool $AutoDeleteMessage = true, string $NotificationText = null, bool $ShowAlert = false): TelegramResponse
        {
            $Response = $this->Telegram->AnswerCallbackQuery($Message->CallbackQueryID, $NotificationText, $ShowAlert);
            if($AutoDeleteMessage)
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
        
                            if($this->Update->Message->Type == MessageType::Pay)
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