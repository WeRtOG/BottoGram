<?php
    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Navigation;

    use WeRtOG\BottoGram\BottoGram;
    use WeRtOG\BottoGram\Telegram\Model\Message;
    use WeRtOG\FoxyMVC\ModelHelper;

    class Menu
    {
        public array $Buttons = [];

        /**
         * Конструктор класса описывающего меню
         * @param $Data Массив данных
         */
        public function __construct(array $Data)
        {
            ModelHelper::SetParametersFromArray($this, $Data);
        }

        public function OnMessage(Message $Message, BottoGram $BottoGram): void {}
        public function OnPay(Message $Message, BottoGram $BottoGram): void {}

        public function OnCallbackQuery(Message $Message, BottoGram $BottoGram): void
        {
            $BottoGram->AnswerCallbackQuery($Message);
        }

        public function OnInit(BottoGram $BottoGram, array $Models = []): void {}
    }
?>