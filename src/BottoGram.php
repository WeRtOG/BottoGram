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
use WeRtOG\BottoGram\Navigation\Menu;
use WeRtOG\BottoGram\Navigation\Command;
use WeRtOG\BottoGram\Telegram\Model\ParseMode;
use WeRtOG\BottoGram\Models\TelegramUser;
use WeRtOG\BottoGram\Models\TelegramUsers;
use WeRtOG\BottoGram\Navigation\ChannelCommand;
use WeRtOG\BottoGram\Navigation\ChatCommand;
use WeRtOG\BottoGram\Navigation\GlobalCommand;
use WeRtOG\BottoGram\Telegram\Model\ReplyKeyboardMarkup;
use WeRtOG\BottoGram\Telegram\Model\Update;
use WeRtOG\BottoGram\Telegram\Model\UpdateType;
use WeRtOG\BottoGram\Telegram\Model\Response;

/**
 * ## BottoGram
 * ##### By WeRtOG
 */
class BottoGram
{
    public Telegram $Telegram;
    
    public ?Log $Log = null;
    public ?TelegramUser $CurrentUser = null;
    public ?Update $Update;
    
    private array $CustomModels = [];
    private array $MenuFoldersList = [];
    private array $Commands = [];

    private Database $Database;

    private TelegramUsers $TelegramUsers;
    private BottoConfig $Config;

    private $InlineQueryAction;
    private $PreCheckoutQueryAction;
    private $ChannelPostAction;

    private string $RootMenu;

    private array|string|null $Keyboard = null;


    function __construct(BottoConfig $Config)
    {
        $this->Config = $Config;

        $this->Telegram = new Telegram(
            Token: $Config->Token,
        );

        $this->Database = self::DatabaseFromConfig($Config);
        $this->TelegramUsers = new TelegramUsers($this->Database);

        $this->RegisterCommand(
            Command: new GlobalCommand(
                Name: BOT_COMMAND_GETID,
                Action: function (Update $Update, TelegramUser $User, Telegram $Telegram)
                {
                    $this->Log?->RequestSuccess();
                    $Telegram->SendMessage($User->ChatID, "👤 Your ID: " . $User->ChatID);
                    exit();
                }
            )
        );
    }

    public static function DatabaseFromConfig(BottoConfig $Config): ?Database
    {
        return DatabaseManager::Connect($Config->DatabaseConnection);
    }

    public function GetCurrentDatabaseInstance(): ?Database 
    {
        return $this->Database;
    }

    public static function GetModel(string $Class, array $Models): mixed
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
    
    public function ConnectMenuFolder(string $Folder, string $Namespace = '', ...$Models): void
    {
        $this->LastMenuFolderPath = $Folder;
        $this->CustomModels = array_merge($this->CustomModels, $Models);

        if(file_exists($Folder))
            $this->MenuFoldersList[] = new MenuFolder(Path: $Folder, Namespace: $Namespace);
    }

    public function SetRootMenu(string $menu): void
    {
        $this->RootMenu = $menu;
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
                    if($Button->Text == $Text) {
                        return $Button->Action;
                    }
                }
            }
        }
        return null;
    }

    public function ReloadMenu(bool $Silent = false): void
    {
        $this->OnNavigated($Silent);
    }

    public function OnNavigated(bool $Silent = false): void
    {
        $CurrentMenu = $this->GetMenuByName($this->CurrentUser->Nav);
        
        if(method_exists($CurrentMenu, 'OnInit'))
            if($CurrentMenu != null) $CurrentMenu->{'OnInit'}($this->CurrentUser, $this->Telegram, $this->CustomModels);

        if($Silent) return;

        $this->Update->Message->Text = BOT_COMMAND_INIT;
        $this->Update->Message->Command = BOT_COMMAND_INIT;
        $this->Keyboard = $CurrentMenu->Buttons ?? null;

        $this->Telegram->SetDefaultReplyMarkup(
            new ReplyKeyboardMarkup(
                Keyboard: $this->Keyboard,
                ResizeKeyboard: $this->Config->ButtonsAutoSize
            )
        );

        
        $Action = isset($CurrentMenu->OnLoad) ? $CurrentMenu->OnLoad : null;

        if(is_callable($Action))
        {
            call_user_func($Action, $this->Update->Message, $this->CurrentUser, $this->Telegram);
        }
        else
        {
            if(method_exists($CurrentMenu, 'OnMessage'))
            {
                $CurrentMenu->{'OnMessage'}($this->Update->Message, $this->CurrentUser, $this->Telegram);
            }
        }
    }

    public function OnError(string $Message, bool $PHPError = true): void
    {
        error_log($Message);

        if($this->CurrentUser == null) return;

        if($this->Log != null)
            $this->Log->RequestFail(500, $Message);
        
        $this->Telegram->SetDefaultReplyMarkup(null);
        if($PHPError)
        {
            $this->Telegram->SendMessage($this->CurrentUser->ChatID, "🐘 <b>Ошибка PHP:</b> \n\n" . $Message, ParseMode: ParseMode::HTML);
            exit();
        }
        else 
        {
            $this->Telegram->SendMessage($this->CurrentUser->ChatID, "⛔️ *Ошибка:* \n\n" . $Message, ParseMode: ParseMode::HTML);
        }    
    }

    public function OnInlineQuery($Action): void
    {
        $this->InlineQueryAction = $Action;
    }

    public function OnPreCheckoutQuery($Action): void
    {
        $this->PreCheckoutQueryAction = $Action;
    }

    public function OnChannelPost($Action): void
    {
        $this->ChannelPostAction = $Action;
    }

    public function RegisterCommand(Command $Command): void
    {
        $this->Commands[] = $Command;
    }

    public function TriggerCommandIfExistsInMessage(?string $MessageCommand, string $CommandType): ?Command
    {
        foreach($this->Commands as $Command)
        {
            if(is_a($Command, $CommandType) || $Command instanceof GlobalCommand)
            {
                if($MessageCommand == $Command->Name && ($this->Update->Type == $Command->UpdateType || $Command->UpdateType == null))
                {
                    $Command->Execute($this->Update, $this->CurrentUser, $this->Telegram);
                    return $Command;
                }
            }
        }

        return null;
    }

    public function Start(): void
    {
        $this->Update = $this->Telegram->GetUpdateFromInput();

        switch($this->Update->Type)
        {
            case UpdateType::Message:
                if(!$this->Update->Message->IsChannelPost)
                {
                    $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                        ChatID: $this->Update->Message->Chat->ID,
                        UserName: $this->Update->Message->Chat->GetUsername(),
                        FullName: $this->Update->Message->Chat->GetFullName()
                    );

                    if($this->Update->Message->MediaGroupID != null && $this->Update->Message->MediaGroupID != $this->CurrentUser->LastMediaGroup)
                    {
                        $this->CurrentUser->SetNewMediaGroup($this->Update->Message->MediaGroupID);
                    }

                    $this->CurrentUser->OnNavigated(function () {
                        $this->OnNavigated();
                    });

                    $this->Log = new Log(
                        ChatID: $this->Update->Message->Chat->ID,
                        Request: $this->Update->Request,
                        Database: $this->Database,
                        EnableTextLog: $this->Config->EnableTextLog,
                        EnableExtendedLog: $this->Config->EnableExtendedLog
                    );

                    if($this->Update->Message->IsFromGroup && !$this->Config->AllowGroups)
                    {
                        $this->Log->RequestFail(403, "Groups not allowed.");
                        exit();
                    }
                }
                else
                {
                    $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                        ChatID: $this->Update->Message->Chat->ID,
                        UserName: 'Channel: ' . $this->Update->Message->Chat->ID,
                        FullName: ''
                    );
                    $this->Log = new Log(
                        ChatID: $this->Update->Message->Chat->ID,
                        Request: $this->Update->Request,
                        Database: $this->Database,
                        EnableTextLog: $this->Config->EnableTextLog,
                        EnableExtendedLog: $this->Config->EnableExtendedLog
                    );
                }
                break;

            case UpdateType::EditedMessage:
                $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                    ChatID: $this->Update->EditedMessage->Chat->ID,
                    UserName: $this->Update->EditedMessage->Chat->GetUsername(),
                    FullName: $this->Update->EditedMessage->Chat->GetFullName()
                );
                $this->Log = new Log(
                    ChatID: $this->Update->EditedMessage->Chat->ID,
                    Request: $this->Update->Request,
                    Database: $this->Database,
                    EnableTextLog: $this->Config->EnableTextLog,
                    EnableExtendedLog: $this->Config->EnableExtendedLog
                );
                break;
            
            case UpdateType::InlineQuery:
                $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                    ChatID: $this->Update->InlineQuery->From->ID,
                    UserName: $this->Update->InlineQuery->From->GetUserName(),
                    FullName: $this->Update->InlineQuery->From->GetFullName()
                );
                $this->Log = new Log(
                    ChatID: $this->Update->InlineQuery->From->ID,
                    Request: $this->Update->Request,
                    Database: $this->Database,
                    EnableTextLog: $this->Config->EnableTextLog,
                    EnableExtendedLog: $this->Config->EnableExtendedLog
                );
                break;

            case UpdateType::PreCheckoutQuery:
                $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                    ChatID: $this->Update->PreCheckoutQuery->From->ID,
                    UserName: $this->Update->PreCheckoutQuery->From->GetUserName(),
                    FullName: $this->Update->PreCheckoutQuery->From->GetFullName()
                );
                $this->Log = new Log(
                    ChatID: $this->Update->PreCheckoutQuery->From->ID,
                    Request: $this->Update->Request,
                    Database: $this->Database,
                    EnableTextLog: $this->Config->EnableTextLog,
                    EnableExtendedLog: $this->Config->EnableExtendedLog
                );
                break;

            case UpdateType::CallbackQuery:
                $this->CurrentUser = $this->TelegramUsers->RegisterUserIfNotExists(
                    ChatID: $this->Update->CallbackQuery->From->ID,
                    UserName: $this->Update->CallbackQuery->From->GetUserName(),
                    FullName: $this->Update->CallbackQuery->From->GetFullName()
                );
                $this->Log = new Log(
                    ChatID: $this->Update->CallbackQuery->From->ID,
                    Request: $this->Update->Request,
                    Database: $this->Database,
                    EnableTextLog: $this->Config->EnableTextLog,
                    EnableExtendedLog: $this->Config->EnableExtendedLog
                );
                break;

        }

        if(isset($this->CurrentUser) && ($this->Config->Private && !in_array($this->CurrentUser->ChatID, $this->Config->PrivateAllow)))
        {
            $this->Log->RequestFail(403, "User not allowed.");
            $this->Telegram->SendMessage($this->CurrentUser->ChatID, "🚫 Access to this bot is restricted.");
            exit();
        }

        if($this->Log != null)
        {
            $this->Telegram->OnResponse(function (Response $Response) {
                print_r($Response->GetData());
                $this->Log->ProcessResponse($Response);
            });
        }

        $this->NavigationLogic();
    }

    private function NavigationLogic(): void
    {
        $Nav = &$this->CurrentUser->Nav;
        if(empty($Nav))
        {
            $Nav = $this->RootMenu;
            $this->CurrentUser->NavigateTo($Nav);
            return;
        }

        $CurrentMenu = $this->GetMenuByName($Nav);
        if($CurrentMenu != null)
        {
            if(method_exists($CurrentMenu, 'OnInit'))
                $CurrentMenu->{'OnInit'}($this->CurrentUser, $this->Telegram, $this->CustomModels);
            
            $this->Keyboard = $CurrentMenu->Buttons ?? null;
            $this->Telegram->SetDefaultReplyMarkup(
                new ReplyKeyboardMarkup(
                    Keyboard: $this->Keyboard,
                    ResizeKeyboard: $this->Config->ButtonsAutoSize
                )
            );

            if(in_array($this->Update->Type, [UpdateType::Message, UpdateType::CallbackQuery]))
            {
                $MessageCommand = $this->Update->Message->Command ?? $this->Update->CallbackQuery->DataCommand ?? null;
                $Command = $this->TriggerCommandIfExistsInMessage($MessageCommand, ChatCommand::class);
                if($Command != null && $Command->ExitAfterExecute) {
                    $this->Log->RequestSuccess();
                    return;
                }
            }

            switch($this->Update->Type)
            {
                case UpdateType::CallbackQuery:
                    if(method_exists($CurrentMenu, 'OnCallbackQuery'))
                    {
                        $CurrentMenu->{'OnCallbackQuery'}($this->Update->CallbackQuery, $this->CurrentUser, $this->Telegram);
                    }
                    break;
                    
                case UpdateType::Message:
                    if(!$this->Update->Message->IsChannelPost)
                    {
                        if($this->Update->Message->Command == BOT_COMMAND_RESET || $this->Update->Message->Command == BOT_COMMAND_START)
                        {
                            $this->CurrentUser->NavigateTo($this->RootMenu);
                            return;
                        }

                        $KeyboardAction = $this->GetKeyboardActionFromMessage($this->Update->Message->Text, $CurrentMenu);
                        if($this->Update->Message->SuccessfulPayment != null)
                        {
                            if(method_exists($CurrentMenu, 'OnSuccessfulPayment')) $CurrentMenu->{'OnSuccessfulPayment'}($this->Update->Message, $this);
                        }
                        else
                        {
                            if($KeyboardAction != null)
                            {
                                if(is_callable($KeyboardAction))
                                {
                                    call_user_func($KeyboardAction, $this->Update->Message, $this->CurrentUser, $this->Telegram);
                                }
                            }
                            else
                            {
                                if(method_exists($CurrentMenu, 'OnMessage'))
                                {
                                    $CurrentMenu->{'OnMessage'}($this->Update->Message, $this->CurrentUser, $this->Telegram);
                                }
                            }
                        }
                    }
                    else
                    {
                        $MessageCommand = $this->Update->Message->Command ?? null;
                        $Command = $this->TriggerCommandIfExistsInMessage($MessageCommand, ChannelCommand::class);
                        $ExecuteOnChannelPostAction = $Command == null || !$Command->ExitAfterExecute;

                        if($ExecuteOnChannelPostAction)
                        {
                            if(is_callable($this->ChannelPostAction))
                            { 
                                call_user_func($this->ChannelPostAction, $this->Update, $this->CurrentUser, $this->Telegram);       
                            }
                        }
                        
                    }
                    break;
                
                case UpdateType::InlineQuery:
                    if(is_callable($this->InlineQueryAction))
                    { 
                        call_user_func($this->InlineQueryAction, $this->Update->InlineQuery, $this->CurrentUser, $this->Telegram);       
                    }
                    break;

                case UpdateType::PreCheckoutQuery:
                    if(is_callable($this->PreCheckoutQueryAction))
                    { 
                        call_user_func($this->PreCheckoutQueryAction, $this->Update->PreCheckoutQuery, $this->CurrentUser, $this->Telegram);       
                    }
                    break;
            }                
        }
        else
        {
            if($this->Update->Type == UpdateType::Message && !$this->Update->Message->IsChannelPost)
                $this->Telegram->SendMessage($this->CurrentUser->ChatID, "*Меню не найдено. 😞*\nДля того, чтобы вернутся в главное меню введите " . BOT_COMMAND_START);
        }

        $this->Log->RequestSuccess();
    }
}