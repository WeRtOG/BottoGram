<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\BottoGram\AdminPanel\BotInfo;
use WeRtOG\BottoGram\BottoConfig;
use WeRtOG\BottoGram\Telegram\Telegram;
use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\Response;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;

class BindingController extends CabinetPageController
{
    public function __construct(array $Models = [])
    {
        parent::__construct($Models);

        if(!$this->AdminPanel->CurrentUser->CanChangeConfig)
        {
            Route::Navigate('');
            exit();
        }
    }

    #[Action]
    public function Index(): View
    {
        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/BindingView.php',
            PageTitle: 'Главная',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php'
        );
    }

    #[Action]
    public function GetBotInfoFromTelegram(): JsonView
    {
        $MainInfo = $this->AdminPanel->TelegramClient->GetMe();
        $WebhookInfo = $this->AdminPanel->TelegramClient->GetWebhookInfo();

        if($MainInfo != null)
        {
            return new JsonView([
                'data' => [
                    'MainInfo' => $MainInfo,
                    'WebhookInfo' => $WebhookInfo ?? null
                ]
            ]);
        }
        else
        {
            return new JsonView([
                'ok' => false,
                'code' => 500,
                'error' => 'Error retrieving telegram bot information via getMe()',
                'error_ru' => 'Ошибка получения информации о боте через getMe()'
            ], 500);
        }
    }

    #[Action(RequestMethod: 'POST')]
    public function ChangeBotToken(): Response
    {
        $NewBotToken = $_POST['BotToken'] ?? null;

        if($NewBotToken != null)
        {
            $TestTelegramClient = new Telegram($NewBotToken);
            $TestInfo = @$TestTelegramClient->GetMe();

            if($TestInfo != null)
            {
                BottoConfig::ChangeParameter('Token', $NewBotToken, $this->AdminPanel->Config->ConfigFile);
            }
            else
            {
                return Route::Navigate('binding/index/?tokenError=1');
            }
        }
        
        return Route::Navigate('binding');
    }

    #[Action(RequestMethod: 'POST')]
    public function ChangeWebhookSettings(): Response
    {
        $WebhookEnabled = isset($_POST['WebhookEnabled']) ? $_POST['WebhookEnabled'] == 'on' : false;
        $WebhookURL = $_POST['WebhookURL'] ?? null;

        if($WebhookEnabled)
        {
            if($WebhookURL != null)
                $this->AdminPanel->TelegramClient->SetWebhook($WebhookURL);

            $WebhookInfo = $this->AdminPanel->TelegramClient->GetWebhookInfo();
            if($WebhookInfo != null)
            {
                if(empty($WebhookInfo->Url))
                    return Route::Navigate('binding/index/?webhookError=' . urlencode('Неправильный URL. Обратите внимание, что домен URL должен иметь SSL-сертификат'));
            }
            else
            {
                return Route::Navigate('binding/index/?webhookError=' . urlencode('Бот с текущим токеном не найден.'));
            }
        }
        else
        {
            $this->AdminPanel->TelegramClient->DeleteWebhook();
        }

        return Route::Navigate('binding');
    }
}