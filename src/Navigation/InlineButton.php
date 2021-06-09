<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Navigation;

    use WeRtOG\FoxyMVC\ModelHelper;

    class InlineButton
    {
        public function __construct(
            public string $Title,
            public ?string $CallbackData = null,
            public ?string $Url = null,
            public ?string $SwitchInlineQueryCurrentChat = null
        )
        { }
    }
?>