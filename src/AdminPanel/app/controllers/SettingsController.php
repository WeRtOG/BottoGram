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
        if($this->AdminPanel->CurrentUser->CanChangeConfig)
            Route::Navigate('settings/config');
        else
            Route::Navigate('settings/profile');

        return new Response('');
    }

    #[Action(RequestMethod: ['GET', 'POST'])]
    public function Config(): View
    {
        if(!$this->AdminPanel->CurrentUser->CanChangeConfig)
        {
            Route::Navigate('settings');
            exit();
        }

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
                            Route::Navigate('settings/config');
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
                            Route::Navigate('settings/config');
                        }
                        else
                        {
                            $AdminPanelFormError = 'Не все поля были заполнены.';
                        }
                        break;

                    case 'UpdateMainInfo':
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

                break;
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'DatabaseFormError' => $DatabaseFormError,
                'AdminPanelFormError' => $AdminPanelFormError,
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsConfigPageView.php'
            ]
        );
    }

    #[Action]
    public function Profile(): View
    {
        $Notify = null;

        if(ActionRequestMethod == 'POST')
        {
            $NewPassword = (string)$_POST['NewPassword'] ?? null;

            if($NewPassword != null)
            {
                $this->AdminPanel->Users->UpdateUserPassword($this->AdminPanel->CurrentUser->ID, $NewPassword);
                $Notify = 'Пароль успешно изменён!';
            }
        }

        return new View(
            ContentView: BOTTOGRAM_MVC_VIEWS . '/pages/SettingsView.php',
            PageTitle: 'Настройки BottoGram',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php',
            Data: [
                'SubPage' => BOTTOGRAM_MVC_VIEWS . '/pages/SettingsProfilePageView.php',
                'Notify' => $Notify
            ]
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
        if(!$this->AdminPanel->CurrentUser->CanManageUsers)
        {
            Route::Navigate('settings');
            exit();
        }

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

    #[Action(RequestMethod: 'POST')]
    public function AddUser(): Response
    {
        if(!$this->AdminPanel->CurrentUser->CanManageUsers)
        {
            Route::Navigate('settings');
            exit();
        }

        $Login = (string)$_POST['Login'] ?? null;
        $Password = (string)$_POST['Password'] ?? null;

        $CanManageUsers = isset($_POST['CanManageUsers']) ? $_POST['CanManageUsers'] == 'on' : false;
        $CanChangeConfig = isset($_POST['CanChangeConfig']) ? $_POST['CanChangeConfig'] == 'on' : false;
        $CanViewRequestLogs = isset($_POST['CanViewRequestLogs']) ? $_POST['CanViewRequestLogs'] == 'on' : false;
        
        if($Login != null && $Password != null)
            $this->AdminPanel->Users->AddUser($Login, $Password, $CanManageUsers, $CanChangeConfig, $CanViewRequestLogs);

        return Route::Navigate('settings/users');
    }

    #[Action(RequestMethod: 'POST')]
    public function EditUser(): Response
    {
        $EditUserID = (int)$_POST['EditUserID'] ?? null;
        $EditUser = $this->AdminPanel->Users->GetUserByID($EditUserID);

        $EditIsNotAllowed = isset($this->AdminPanel->CurrentUser) && (
            $this->AdminPanel->CurrentUser->Login == $EditUser->Login
            || $EditUser->Login == 'admin' 
            || (
                $EditUser->CanManageUsers 
                && $this->AdminPanel->CurrentUser->Login != 'admin'
            )
        );

        if(!$this->AdminPanel->CurrentUser->CanManageUsers || $EditIsNotAllowed)
        {
            Route::Navigate('settings');
            exit();
        }

        if($EditUserID != null)
        {
            $NewPassword = (string)$_POST['NewPassword'] ?? null;

            $CanManageUsers = isset($_POST['CanManageUsers']) ? $_POST['CanManageUsers'] == 'on' : false;
            $CanChangeConfig = isset($_POST['CanChangeConfig']) ? $_POST['CanChangeConfig'] == 'on' : false;
            $CanViewRequestLogs = isset($_POST['CanViewRequestLogs']) ? $_POST['CanViewRequestLogs'] == 'on' : false;
    
            if($NewPassword != null)
            {
                $this->AdminPanel->Users->UpdateUserPassword($EditUserID, $NewPassword);
            }

            $this->AdminPanel->Users->UpdateUserRights($EditUserID, $CanManageUsers, $CanChangeConfig, $CanViewRequestLogs);
        }

        return Route::Navigate('settings/users');
    }

    #[Action(RequestMethod: 'POST')]
    public function DeleteUser(): Response
    {
        $DeleteUserID = (int)$_POST['DeleteUserID'] ?? null;
        $DeleteUser = $this->AdminPanel->Users->GetUserByID($DeleteUserID);

        $DeleteIsNotAllowed = isset($this->AdminPanel->CurrentUser) && (
            $this->AdminPanel->CurrentUser->Login == $DeleteUser->Login
            || $DeleteUser->Login == 'admin' 
            || (
                $DeleteUser->CanManageUsers 
                && $this->AdminPanel->CurrentUser->Login != 'admin'
            )
        );

        if(!$this->AdminPanel->CurrentUser->CanManageUsers || $DeleteIsNotAllowed)
        {
            Route::Navigate('settings');
            exit();
        }
        
        if($DeleteUserID != null)
            $this->AdminPanel->Users->DeleteUser($DeleteUserID);

        return Route::Navigate('settings/users');
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
                    setcookie('dark-theme', 1, strtotime('+2000 days'), '/');
                    return new JsonView(['ok' => true]);
                case 'white':
                    setcookie('dark-theme', 0, strtotime('+2000 days'), '/');
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