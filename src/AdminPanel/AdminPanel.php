<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

require_once 'Constants.php';

use Error;
use Exception;
use WeRtOG\BottoGram\AdminPanel\Models\AdminUser;
use WeRtOG\BottoGram\AdminPanel\Models\AdminUsers;
use WeRtOG\BottoGram\AdminPanel\Models\RequestLogs;
use WeRtOG\BottoGram\AdminPanel\Models\SidebarCustomItems;
use WeRtOG\BottoGram\AdminPanel\Optimization\FrontendMinifer;
use WeRtOG\BottoGram\AdminPanel\Optimization\MiniferMap;
use WeRtOG\BottoGram\BottoConfig;
use WeRtOG\BottoGram\DatabaseManager\Database;
use WeRtOG\BottoGram\DatabaseManager\DatabaseManager;
use WeRtOG\BottoGram\Models\TelegramUsers;
use WeRtOG\BottoGram\Telegram\Telegram;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\Response;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;

class AdminPanel
{
    public BottoConfig $Config;
    public Database $Database;
    public AccessControl $AccessControl;
    public Analytics $Analytics;
    public RequestLogs $RequestLogs;
    public AdminUsers $Users;
    public TelegramUsers $TelegramUsers;
    public ?AdminUser $CurrentUser;
    public Telegram $TelegramClient;

    public function __construct(BottoConfig $Config)
    {
        define('BOTTOGRAM_MVC_ROOT', __DIR__ . '/app');
        define('BOTTOGRAM_MVC_MODELS', BOTTOGRAM_MVC_ROOT . '/models');
        define('BOTTOGRAM_MVC_VIEWS', BOTTOGRAM_MVC_ROOT . '/views');
        define('BOTTOGRAM_MVC_CONTROLLERS', BOTTOGRAM_MVC_ROOT . '/controllers');

        self::HandleExceptions();

        Route::ConnectFolder(BOTTOGRAM_MVC_MODELS);
        Route::ConnectFolder(BOTTOGRAM_MVC_CONTROLLERS . '/general');
        Route::ConnectFolder(BOTTOGRAM_MVC_CONTROLLERS);

        $this->Config = $Config;
        $this->Database = DatabaseManager::Connect($Config->DatabaseConnection);
        $this->AccessControl = new AccessControl($this->Database, $Config->SessionUser);
        $this->Analytics = new Analytics($this->Database);
        $this->RequestLogs = new RequestLogs($this->Database);
        $this->Users = new AdminUsers($this->Database);
        $this->TelegramUsers = new TelegramUsers($this->Database);

        $UserName = $this->AccessControl->GetUserName();

        $this->CurrentUser = $this->Users->GetUserByLogin($UserName);
        $this->TelegramClient = new Telegram($this->Config->Token);
        

        $this->TelegramClient->OnResponse(function($Result) {
            $Result->GetData();
        });
    }

    public static function OnError(Error $Error): void
    {
        if(ActionReturnType == 'HTML')
        {
            Response::Send(new View(
                ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/CodeErrorView.php',
                PageTitle: 'Unexpected error',
                TemplateView: BOTTOGRAM_MVC_VIEWS . '/Error500TemplateView.php',
                Data: [
                    'Error' => $Error
                ]
            ));
        }
        else if(ActionReturnType == 'JSON')
        {
            Response::Send(new JsonView(['ok' => false, 'code' => 500, 'error' => (string)$Error]));
        }
        exit();
    }

    public static function OnException(Exception $Exception): void
    {
        if(ActionReturnType == 'HTML')
        {
            Response::Send(new View(
                ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/CodeExceptionView.php',
                PageTitle: 'Unexpected exception',
                TemplateView: BOTTOGRAM_MVC_VIEWS . '/Error500TemplateView.php',
                Data: [
                    'Exception' => $Exception
                ]
            ));
        }
        else if(ActionReturnType == 'JSON')
        {
            Response::Send(new JsonView(['ok' => false, 'code' => 400, 'error' => (string)$Exception]));
        }
        exit();
    }

    public static function HandleExceptions(): void
    {
        ob_start();

        $Handler = function ($Exception) {
            @ob_clean();
            @ob_end_flush();
            
            if(!defined('ActionReturnType'))
                define('ActionReturnType', 'HTML');
            
            if($Exception instanceof Exception)
                self::OnException($Exception);
            else if($Exception instanceof Error)
                self::OnError($Exception);
        };

        set_exception_handler($Handler);
        set_error_handler($Handler);
    }

    public static function GetBuiltInСomponentsPathIntOffset(): int
    {
        if(defined('BOTTOGRAM_FR_ADMINPANEL_PATH') && defined('BOTTOGRAM_FR_PROJECTROOT_PATH'))
        {
            return Route::CalculatePathIntOffset(BOTTOGRAM_FR_ADMINPANEL_PATH, BOTTOGRAM_FR_PROJECTROOT_PATH);
        }
    }

    public static function ConnectJS(View $View, string $Path, bool $IsProductionAsset = false): void
    {
        if(!file_exists($Path) && $IsProductionAsset)
        {
            self::ReloadMinifedAssets();
            if(file_exists($Path))
            {
                $View->LoadJS($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
                return; 
            }

            echo "<script>alert('Error: resource not exists. Path: $Path'); </script>";
            return;
        }

        $View->LoadJS($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
    }

    public static function AsyncConnectJS(View $View, string $Path, bool $IsProductionAsset = false): void
    {
        if(!file_exists($Path) && $IsProductionAsset)
        {
            self::ReloadMinifedAssets();
            if(file_exists($Path))
            {
                $PublicPath = $View->GenerateFilePublicPath($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
                echo '<script>PageScripts.LoadPageScript("' . $PublicPath . '")</script>';
                return; 
            }

            echo "<script>alert('Error: resource not exists. Path: $Path'); </script>";
            return;
        }

        $PublicPath = $View->GenerateFilePublicPath($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
        echo '<script>PageScripts.LoadPageScript("' . $PublicPath . '")</script>';
    }

    public static function ConnectCSS(View $View, string $Path, bool $IsProductionAsset = false): void
    {
        if(!file_exists($Path) && $IsProductionAsset)
        {
            self::ReloadMinifedAssets();
            if(file_exists($Path))
            {
                $View->LoadCSS($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
                return; 
            }

            echo "<script>alert('Error: resource not exists. Path: $Path'); </script>";
            return;
        }

        $View->LoadCSS($Path, AdminPanel::GetBuiltInСomponentsPathIntOffset());
    }

    public static function ReloadMinifedAssets(): void
    {
        $Map = MiniferMap::FromJSONFile(BOTTOGRAM_ADMIN_ASSETS . '/minify-map.json', BOTTOGRAM_ADMIN_ASSETS);
        if($Map != null)
        {
            FrontendMinifer::MinifyFromMap($Map);
        }
    }

    public function Start(string $Namespace, string $AdminPanelPath, string $ProjectRootPath = '', SidebarCustomItems $SidebarCustomItems = null, string $CustomControllersFolder = null, array $CustomModels = []): void
    {
        /*
        $Map = MiniferMap::FromJSONFile(BOTTOGRAM_ADMIN_ASSETS . '/minify-map.json', BOTTOGRAM_ADMIN_ASSETS);
        if($Map != null)
        {
            $Result = FrontendMinifer::MinifyFromMap($Map);
            print_r($Result);
        }

        return
        */
        if($ProjectRootPath == '')
            $ProjectRootPath = $AdminPanelPath;

        // Utility constants for calculating the path to embedded components
        define('BOTTOGRAM_FR_ADMINPANEL_PATH', $AdminPanelPath);
        define('BOTTOGRAM_FR_PROJECTROOT_PATH', $ProjectRootPath);

        $this->GlobalData['SidebarCustomItems'] = $SidebarCustomItems;
        
        if($CustomControllersFolder)
            Route::ConnectFolder($CustomControllersFolder);
        
        Route::Start(
            ProjectNamespace: ['WeRtOG\BottoGram\AdminPanel\MVC', dirname(IndexController::class), $Namespace],
            ProjectPath: $AdminPanelPath,
            Models: array_merge([
                'AdminPanel' => $this,
            ], $CustomModels),
            GlobalData: [
                'UseMinifedAssets' => $this->Config->UseMinifedAssetsInAdminPanel,
                'DarkTheme' => $_COOKIE['dark-theme'] ?? false,
                'CurrentUser' => $this->CurrentUser,
                'BottoConfig' => $this->Config,
                'SidebarCustomItems' => $SidebarCustomItems
            ]
        );
    }
}