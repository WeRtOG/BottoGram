<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\BottoGram\AdminPanel\MVC\general\CabinetPageController;
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

            $RequestLogs = $this->AdminPanel->RequestLogs->GetLogs(BOTTOGRAM_ADMIN_REQUESTLOGSLIMIT);

            if($this->AdminPanel->CurrentUser->Login == 'admin')
            {
                foreach($RequestLogs as &$RequestLog)
                {
                    $UserPage = ceil($RequestLog->UserID / BOTTOGRAM_ADMIN_PAGELIMIT);
                    $RequestLog->UserURL = Route::GetRoot() . '/fordevelopers/botusers/?page=' . $UserPage. '&highlight=' . $RequestLog->UserID;
                }
            }

            $NewChecksum = md5(json_encode($RequestLogs));

            if($NewChecksum != $LastChecksum)
            {
                return new JsonView([
                    'ok' => true,
                    'checksum' => $NewChecksum,
                    'hasNewData' => true,
                    'logs' => $RequestLogs
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
