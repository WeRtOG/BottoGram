<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\MVC\general;

use WeRtOG\BottoGram\AdminPanel\AdminPanel;
use WeRtOG\FoxyMVC\Controller;
use WeRtOG\FoxyMVC\Route;

class CabinetPageController extends Controller
{
    public AdminPanel $AdminPanel;
    
    public function __construct(array $Models = [])
    {
        parent::__construct($Models);

        if(!$this->AdminPanel->AccessControl->IsAuthorized())
            Route::Navigate('auth');
    }
}
