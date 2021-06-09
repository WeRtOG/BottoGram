<?php

    /*
        WeRtOG
        BottoGram
    */
	namespace WeRtOG\BottoGram\AdminPanel\MVC;

    use WeRtOG\FoxyMVC\Attributes\Action;
    use WeRtOG\FoxyMVC\Controller;
    use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
    use WeRtOG\FoxyMVC\ControllerResponse\Response;
    use WeRtOG\FoxyMVC\ControllerResponse\View;
    use WeRtOG\FoxyMVC\Route;

    class LogsController extends CabinetPageController
    {
        #[Action]
        public function Index(): View
        {
            return new View(
                ContentView: BOTTOGRAM_MVC_VIEWS . '/Pages/LogsView.php',
                PageTitle: 'История запросов',
                TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php'
            );
        }

        #[Action]
        public function GetData(): JsonView
        {
            $Logs = $this->AdminPanel->Log->GetLogs();
            return new JsonView([
                'ok' => true,
                'checksum' => md5(json_encode($Logs)),
                'logs' => $Logs
            ]);
        }
    }
?>