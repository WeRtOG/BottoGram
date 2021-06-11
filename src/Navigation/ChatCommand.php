<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Navigation;
    
    class ChatCommand extends Command {
        public function __construct(
            public string $Name,
            public mixed $Action,
            public bool $ExitAfterExecute = true,
            public bool $IgnoreGroupAllow = false,
        )
        { }
    }
?>