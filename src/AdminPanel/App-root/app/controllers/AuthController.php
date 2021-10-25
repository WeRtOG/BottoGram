<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC;

use WeRtOG\FoxyMVC\Attributes\Action;
use WeRtOG\FoxyMVC\Controller;
use WeRtOG\FoxyMVC\ControllerResponse\View;
use WeRtOG\FoxyMVC\Route;

class AuthController extends Controller
{
    #[Action]
    public function Index(): View
    {
        if($this->AdminPanel->AccessControl->IsAuthorized())
            Route::NavigateToRoot();

        $Error = null;
        switch(ActionRequestMethod)
        {
            case 'POST':
                $Login = $_POST['Login'] ?? null;
                $Password = $_POST['Password'] ?? null;
                
                if(!empty($Login) && !empty($Password))
                {
                    $LoginSuccess = $this->AdminPanel->AccessControl->DoLogin($Login, $Password);
                    if($LoginSuccess)
                    {
                        Route::NavigateToRoot();
                    }
                    else
                    {
                        $Error = 'Неверный логин или пароль.';
                    }
                }
                else
                {
                    $Error = 'Не все поля заполнены.';
                }

                break;
        }

        return new View(
            ContentView: '',
            PageTitle: 'Необходима авторизация',
            TemplateView: BOTTOGRAM_MVC_VIEWS . '/AuthView.php',
            Data: $Error != null ? ['Error' => $Error] : []
        );
    }

    #[Action(RequestMethod: ['GET'])]
    public function Logout(): void
    {
        $this->AdminPanel->AccessControl->DoLogout();
        Route::Navigate('auth');
    }
}