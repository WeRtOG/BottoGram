<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\BottoGram\AdminPanel\Models\SystemInfo;
use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\Response;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;

class ForDevelopersController extends CabinetPageController
{
    #[Action]
    public function Index(): Response
    {
        return Route::Navigate('fordevelopers/logs');
    }

    #[Action]
    public function Logs(): View
    {
        if(!$this->AdminPanel->CurrentUser->Login == 'admin')
        {
            Route::Navigate('');
            exit();
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersView.php',
            PageTitle: 'Для разработчиков',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: ['SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersLogsView.php']
        );
    }

    #[Action]
    public function GetLogsRAW(): JsonView
    {
        if($this->AdminPanel->CurrentUser->Login == 'admin')
        {
            $LastChecksum = $_GET['checksum'] ?? null;

            $LogsRaw = @file_get_contents(BOTTOGRAM_FR_PROJECTROOT_PATH . '/app-error.log');
            $LogsRaw = empty($LogsRaw) ? '' : $LogsRaw;
            $NewChecksum = md5($LogsRaw);

            if($NewChecksum != $LastChecksum)
            {
                return new JsonView([
                    'ok' => true,
                    'code' => '200',
                    'checksum' => $NewChecksum,
                    'hasNewData' => true,
                    'raw' => $LogsRaw
                ]);
            }
            else
            {
                return new JsonView([
                    'ok' => true,
                    'code' => '200',
                    'hasNewData' => false,
                    'checksum' => $NewChecksum
                ]);
            }
        }
        else
        {
            return new JsonView([
                'ok' => false,
                'code' => '403',
                'error' => 'No access.',
                'error_ru' => 'Нет доступа.'
            ], 403);
        }
    }

    
    #[Action]
    public function ClearLogs(): Response
    {
        if(!$this->AdminPanel->CurrentUser->Login == 'admin')
        {
            Route::Navigate('');
            exit();
        }

        $LogsPath = BOTTOGRAM_FR_PROJECTROOT_PATH . '/app-error.log';
        if(file_exists($LogsPath))
        {
            file_put_contents($LogsPath, '');
        }

        return Route::Navigate('fordevelopers/logs');
    }

    #[Action]
    public function BotUsers(): View
    {
        if(!$this->AdminPanel->CurrentUser->Login == 'admin')
        {
            Route::Navigate('');
            exit();
        }

        $Page = (int)($_GET['page'] ?? 1);
        if($Page <= 0) $Page = 1;

        $Highlight = (int)($_GET['highlight'] ?? 0);

        $PageCount = $this->AdminPanel->TelegramUsers->GetAllUsersPagesCount(BOTTOGRAM_ADMIN_PAGELIMIT);
        $Users = $this->AdminPanel->TelegramUsers->GetAllUsers($Page, BOTTOGRAM_ADMIN_PAGELIMIT);

        $PaginationLeft = $Page - 5;
        if($PaginationLeft < 1) $PaginationLeft = 1;
        
        $PaginationRight = $PaginationLeft + 10;
        if($PaginationRight > $PageCount) $PaginationRight = $PageCount;

        $PaginationDiff = $PaginationRight - $PaginationLeft;
        if($PaginationDiff < 10) $PaginationLeft -= 10 - $PaginationDiff;
        
        if($PaginationLeft < 1) $PaginationLeft = 1;
        

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersView.php',
            PageTitle: 'Для разработчиков',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'CurrentPage' => $Page,
                'PageCount' => $PageCount,
                'PaginationLeft' => $PaginationLeft,
                'PaginationRight' => $PaginationRight,
                'Highlight' => $Highlight,
                'Users' => $Users,
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersBotUsersView.php'
            ]
        );
    }

    #[Action]
    public function SearchBotUsers(): JsonView
    {
        $Query = (string)($_GET['query'] ?? null);
        
        if(!empty($Query))
        {
            $Results = $this->AdminPanel->TelegramUsers->SearchUsers($Query, BOTTOGRAM_ADMIN_SEARCHLIMIT, BOTTOGRAM_ADMIN_PAGELIMIT);
        
            return new JsonView([
                'ok' => true,
                'code' => 200,
                'checksum' => md5(json_encode($Results)),
                'results' => $Results
            ]);
        }
        else
        {
            return new JsonView([
                'ok' => true,
                'code' => 400,
                'error' => 'Bad request: query field is empty.',
                'error_ru' => 'Неверный запрос: поле query пустое.'
            ], 400);
        }
    }

    
    #[Action]
    public function SystemInfo(): View
    {
        if(!$this->AdminPanel->CurrentUser->Login == 'admin')
        {
            Route::Navigate('');
            exit();
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersView.php',
            PageTitle: 'Для разработчиков',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'SystemInfo' => [
                    'OS' => php_uname(),
                    'PHPVersion' => phpversion(),
                    'MySQLVersion' => $this->AdminPanel->Database->GetServerVersion() ?? 'неизвестно',
                    'BottoGramVersion' => SystemInfo::GetBottoGramVersion(),
                    'BottoGramPath' => BOTTOGRAM_ROOT,
                    'ProjectPath' => BOTTOGRAM_FR_PROJECTROOT_PATH,
                    'AdminPanelPath' => BOTTOGRAM_FR_ADMINPANEL_PATH
                ],
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersSystemInfoView.php'
            ]
        );
    }
}