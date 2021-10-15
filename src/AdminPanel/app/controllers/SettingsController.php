<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use Exception;
use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\ControllerResponse\JsonView;
use WeRtOG\FoxyMVC\ControllerResponse\Response;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;
use WeRtOG\BottoGram\BottoConfig;
use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseConnection;
use WeRtOG\BottoGram\DatabaseManager\DatabaseManager;

class SettingsController extends CabinetPageController
{
    #[Action]
    public function Index(): Response
    {
        Route::Navigate('settings/main');
        return new Response('');
    }

    #[Action]
    public function Main(): View
    {
        $DatabaseFormError = null;
        $AdminPanelFormError = null;

        switch(ActionRequestMethod)
        {
            case 'POST':
                $POSTForm = $_POST['form'] ?? null;
                switch($POSTForm)
                {
                    case 'UpdateDatabase': 
                        $DatabaseServer = $_POST['DatabaseServer'] ?? null;
                        $DatabaseUser = $_POST['DatabaseUser'] ?? null;
                        $DatabasePassword = $_POST['DatabasePassword'] ?? null;
                        $DatabaseName = $_POST['DatabaseName'] ?? null;
    
                        try
                        {
                            $DatabaseConnection = new DatabaseConnection([
                                'Server' => $DatabaseServer,
                                'User' => $DatabaseUser,
                                'Password' => $DatabasePassword,
                                'Database' => $DatabaseName
                            ]);
    
                            DatabaseManager::Connect($DatabaseConnection);
    
                            BottoConfig::ChangeParameter('DatabaseConnection', $DatabaseConnection, $this->AdminPanel->Config->ConfigFile);
                            Route::Navigate('settings/main');
                        }
                        catch(Exception $Exception)
                        {
                            $DatabaseFormError = $Exception->getMessage();
                        }
                        break;
                    
                    case 'UpdateAdminPanelSettings': 
                        $SessionUser = $_POST['SessionUser'] ?? null;

                        if($SessionUser != null)
                        {
                            BottoConfig::ChangeParameter('SessionUser', $SessionUser, $this->AdminPanel->Config->ConfigFile);
                            Route::Navigate('settings/main');
                        }
                        else
                        {
                            $AdminPanelFormError = 'Не все поля были заполнены.';
                        }
                        break;
                }

                break;
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'DatabaseFormError' => $DatabaseFormError,
                'AdminPanelFormError' => $AdminPanelFormError,
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsMainPageView.php'
            ]
        );
    }

    #[Action(RequestMethod: ['GET', 'POST'])]
    public function Config(): View
    {
        switch(ActionRequestMethod)
        {
            case 'POST':
                $POSTParameters = [
                    'Name' => (string)$_POST['BottoConfig_Name'] ?? null,
                    'AllowGroups' => isset($_POST['BottoConfig_AllowGroups']) ? $_POST['BottoConfig_AllowGroups'] == 'on' : false,
                    'EnableTextLog' => isset($_POST['BottoConfig_EnableTextLog']) ? $_POST['BottoConfig_EnableTextLog'] == 'on' : false,
                    'EnableExtendedLog' => isset($_POST['BottoConfig_EnableExtendedLog']) ? $_POST['BottoConfig_EnableExtendedLog'] == 'on' : false,
                    'ButtonsAutoSize' => isset($_POST['BottoConfig_ButtonsAutoSize']) ? $_POST['BottoConfig_ButtonsAutoSize'] == 'on' : false
                ];
                
                if($POSTParameters['Name'] != null)
                {
                    foreach($POSTParameters as $Parameter => $Value)
                    {
                        if($Value != null && $Value != "" || is_bool($Value))
                        {
                            BottoConfig::ChangeParameter($Parameter, $Value, $this->AdminPanel->Config->ConfigFile);
                        }
                    }
                }

                Route::Navigate('settings/config');

                break;
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: ['SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsConfigPageView.php']
        );
    }

    #[Action]
    public function Personalization(): View
    {
        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: ['SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsPersonalizationPageView.php']
        );
    }
    
    #[Action(RequestMethod: 'GET')]
    public function Users(): View
    {
        $Users = $this->AdminPanel->Users->GetUsers();

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'Users' => $Users,
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsUsersPageView.php'
            ]
        );
    }

    #[Action(RequestMethod: 'GET')]
    public function SwitchTheme(): JsonView
    {
        $Theme = $_GET['theme'] ?? null;

        if($Theme != null)
        {
            switch($Theme)
            {
                case 'dark':
                    BottoConfig::ChangeParameter('DarkTheme', true, $this->AdminPanel->Config->ConfigFile);
                    return new JsonView(['ok' => true]);
                case 'white':
                    BottoConfig::ChangeParameter('DarkTheme', false, $this->AdminPanel->Config->ConfigFile);
                    return new JsonView(['ok' => true]);
                default:
                return new JsonView([
                    'ok' => false,
                    'code' => '400',
                    'error' => 'Theme field value is invalid.',
                    'error_ru' => 'Значение поля темы неверное.'
                ]);
            }
        }
        else
        {
            return new JsonView([
                'ok' => false,
                'code' => '400',
                'error' => 'Theme field is empty.',
                'error_ru' => 'Поле темы пустое.'
            ]);
        }
    }
}