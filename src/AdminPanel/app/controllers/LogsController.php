<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;

class LogsController extends CabinetPageController
{
    #[Action]
    public function Index(): View
    {
        if(!$this->AdminPanel->CurrentUser->CanChangeConfig)
        {
            Route::Navigate('settings');
            exit();
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/LogsView.php',
            PageTitle: 'История запросов',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php'
        );
    }

    #[Action]
    public function GetData(): JsonView
    {
        if($this->AdminPanel->CurrentUser->CanViewLogs)
        {
            $Logs = $this->AdminPanel->Log->GetLogs();
            return new JsonView([
                'ok' => true,
                'checksum' => md5(json_encode($Logs)),
                'logs' => $Logs
            ]);
        }
        else
        {
            return new JsonView([
                'ok' => false,
                'code' => 403,
                'error' => 'No access.',
                'error_ru' => 'Нет доступа.',
            ], 403);
        }
    }
}