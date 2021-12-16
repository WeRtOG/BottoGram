<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\BottoGram\AdminPanel\Models\BotInfo;
use WeRtOG\BottoGram\AdminPanel\MVC\general\CabinetPageController;
use WeRtOG\BottoGram\AdminPanel\Utils;
use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\View;

class DashboardController extends CabinetPageController
{
    #[Action]
    public function Index(): View
    {
        $UsersCount = $this->AdminPanel->Analytics->GetUsersCount();
        $RequestsCount = $this->AdminPanel->Analytics->GetRequestsCount();
        $DailyUsersCount = $this->AdminPanel->Analytics->GetDailyUsersCount();
        $DailyRequestsCount = $this->AdminPanel->Analytics->GetDailyRequestsCount();
        $WeeklyUsersCount = $this->AdminPanel->Analytics->GetWeeklyUsersCount();
        $WeeklyRequestsCount = $this->AdminPanel->Analytics->GetWeeklyRequestsCount();

        $Analytics = [
            'UsersCount' => $UsersCount,
            'UsersCountString' => '<b>' . Utils::NumWord($UsersCount, ['пользователь', 'пользователя', 'пользователей']) . '</b> за всё время',
            'RequestsCount' => $RequestsCount,
            'RequestsCountString' => '<b>' . Utils::NumWord($RequestsCount, ['запрос', 'запроса', 'запросов']) . '</b> за всё время',
            'DailyUsersCount' => $DailyUsersCount,
            'DailyUsersCountString' => '<b>' . Utils::NumWord($DailyUsersCount, ['пользователь</b> пользовался', 'пользователя</b> пользовались', 'пользователей</b> пользовались']) . ' ботом сегодня',
            'DailyRequestsCount' => $DailyRequestsCount,
            'DailyRequestsCountString' => '<b>' . Utils::NumWord($DailyRequestsCount, ['запрос', 'запроса', 'запросов']) . '</b> за день',
            'WeeklyUsersCount' => $WeeklyUsersCount,
            'WeeklyUsersCountString' => '<b>' . Utils::NumWord($WeeklyUsersCount, ['пользователь</b> пользовался', 'пользователя</b> пользовались', 'пользователей</b> пользовались']) . ' ботом на этой неделе',
            'WeeklyRequestsCount' => $WeeklyRequestsCount,
            'WeeklyRequestsCountString' => '<b>' . Utils::NumWord($WeeklyRequestsCount, ['запрос', 'запроса', 'запросов']) . '</b> за неделю',

            'WeeklyUsersGraph' => $this->AdminPanel->Analytics->GetWeeklyUsersGraph(),
            'DailyUsersGraph' => $this->AdminPanel->Analytics->GetDayUsersGraph(),
            'NewUsersGraph' => $this->AdminPanel->Analytics->GetNewUsersGraph(),
            'Timezone' => $this->AdminPanel->Analytics->GetTimezone()
        ];

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/DashboardView.php',
            PageTitle: 'Главная',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'Analytics' => $Analytics
            ]
        );
    }

    #[Action]
    public function GetBotInfo(): JsonView
    {
        $BotWebhook = $this->AdminPanel->TelegramClient->GetWebhookInfo();
        $BotAccount = $this->AdminPanel->TelegramClient->GetMe();

        $BotInfo = new BotInfo(!empty($BotWebhook->Url), $BotWebhook, $BotAccount);
        
        return new JsonView([
            'info' => $BotInfo
        ]);
    }
}
