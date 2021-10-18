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

class RequestsController extends CabinetPageController
{
    #[Action]
    public function Index(): View
    {
        if(!$this->AdminPanel->CurrentUser->CanViewRequestLogs)
        {
            Route::Navigate('');
            exit();
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/RequestsView.php',
            PageTitle: 'История запросов',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php'
        );
    }

    #[Action]
    public function GetData(): JsonView
    {   
        if($this->AdminPanel->CurrentUser->CanViewRequestLogs)
        {
            $LastChecksum = $_GET['checksum'] ?? null;

            $Logs = $this->AdminPanel->Log->GetLogs();
            $NewChecksum = md5(json_encode($Logs));

            if($NewChecksum != $LastChecksum)
            {
                return new JsonView([
                    'ok' => true,
                    'checksum' => $NewChecksum,
                    'hasNewData' => true,
                    'logs' => $Logs
                ]);
            }
            else
            {
                return new JsonView([
                    'ok' => true,
                    'hasNewData' => false,
                    'checksum' => $NewChecksum
                ]);
            }
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