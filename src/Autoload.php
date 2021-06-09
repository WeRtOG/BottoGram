<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram;

    include __DIR__ . '/Constants.php';

    spl_autoload_register(function(string $ClassName) {
        $ClassName = str_replace(__NAMESPACE__ . '\\', '', $ClassName);

        $ClassFilename = __DIR__ . '/' . $ClassName . '.php';

        //echo "Ищу файл: $ClassFilename\n";
        

        if(file_exists($ClassFilename))
        {
            //echo "Нашёл, подключил.\n\n";
            require_once $ClassFilename;
        }
        else
        {
            //echo "Не нашёл.\n\n";
        }
    });
?>