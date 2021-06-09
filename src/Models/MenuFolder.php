<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Models;

    class MenuFolder
    {
        public function __construct(
            public string $Path,
            public string $Namespace
        )
        { }
    }
?>