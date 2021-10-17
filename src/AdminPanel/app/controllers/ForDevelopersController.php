<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\BottoGram\AdminPanel\SystemInfo;
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

        $LogsList = @file_get_contents(BOTTOGRAM_FR_PROJECTROOT_PATH . '/app-error.log');
        $LogsList = empty($LogsList) ? '' : $LogsList;

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersView.php',
            PageTitle: 'Для разработчиков',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: ['LogsList' => $LogsList, 'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersLogsView.php']
        );
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
                    'BottoGramVersion' => SystemInfo::GetBottoGramVersion()
                ],
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/ForDevelopersSystemInfoView.php'
            ]
        );
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
}