<?php

    /*
        WeRtOG
        BottoGram
    */
	namespace WeRtOG\BottoGram\AdminPanel\MVC;

    use WeRtOG\FoxyMVC\Controller;
    use WeRtOG\FoxyMVC\Route;

    class IndexController extends Controller
	{
		public function __construct(array $Models = [])
        {
            parent::__construct($Models);

            if(!$this->AdminPanel->AccessControl->IsAuthorized())
                Route::Navigate('auth');
            else
                Route::Navigate('dashboard');
        }
	}
?>