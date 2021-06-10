<?php

    /*
        WeRtOG
        BottoGram
    */
	namespace WeRtOG\BottoGram\AdminPanel\MVC;

    use WeRtOG\FoxyMVC\Attributes\Action;
    use WeRtOG\FoxyMVC\ControllerResponse\View;

    class DashboardController extends CabinetPageController
    {
        #[Action]
        public function Index(): View
        {
            $Analytics = [
                "UsersCount" => $this->AdminPanel->Analytics->GetUsersCount(),
                "RequestsCount" => $this->AdminPanel->Analytics->GetRequestsCount(),
                "DailyUsersCount" => $this->AdminPanel->Analytics->GetDailyUsersCount(),
                "DailyRequestsCount" => $this->AdminPanel->Analytics->GetDailyRequestsCount(),
                "WeeklyUsersCount" => $this->AdminPanel->Analytics->GetWeeklyUsersCount(),
                "WeeklyRequestsCount" => $this->AdminPanel->Analytics->GetWeeklyRequestsCount(),
                "WeeklyUsersGraph" => $this->AdminPanel->Analytics->GetWeeklyUsersGraph(),
                "DailyUsersGraph" => $this->AdminPanel->Analytics->GetDayUsersGraph(),
                "NewUsersGraph" => $this->AdminPanel->Analytics->GetNewUsersGraph(),
                "Timezone" => $this->AdminPanel->Analytics->GetTimezone()
            ];

            return new View(
                ContentView: BOTTOGRAM_MVC_VIEWS . '/Pages/DashboardView.php',
                PageTitle: 'Главная',
                TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
                Data: [
                    'Analytics' => $Analytics
                ]
            );
        }
    }
?>