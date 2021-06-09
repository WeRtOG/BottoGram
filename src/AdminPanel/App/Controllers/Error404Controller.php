<?php

    /*
        WeRtOG
        BottoGram
    */
	namespace WeRtOG\BottoGram\AdminPanel\MVC;

    use WeRtOG\FoxyMVC\Attributes\Action;
    use WeRtOG\FoxyMVC\Controller;
    use WeRtOG\FoxyMVC\ControllerResponse\Response;
    use WeRtOG\FoxyMVC\ControllerResponse\View;
    use WeRtOG\FoxyMVC\Route;

    class Error404Controller extends Controller
	{
		/**
         * Экшн коренной страницы
         */
        #[Action]
		public function Index(): View
		{
            return new View(
                ContentView: BOTTOGRAM_MVC_VIEWS . '/Pages/Error404View.php',
                PageTitle: '404',
                TemplateView: BOTTOGRAM_MVC_VIEWS . '/CabinetView.php'
            );
		}
	}
?>