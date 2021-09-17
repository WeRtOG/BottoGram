<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Navigation;

use WeRtOG\BottoGram\BottoGram;
use WeRtOG\BottoGram\Models\TelegramUser;
use WeRtOG\BottoGram\Telegram\Model\CallbackQuery;
use WeRtOG\BottoGram\Telegram\Model\Message;
use WeRtOG\BottoGram\Telegram\Telegram;
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

    public function OnMessage(Message $Message, TelegramUser $User, Telegram $Telegram): void {}
    public function OnPay(Message $Message, TelegramUser $User, Telegram $Telegram): void {}

    public function OnCallbackQuery(CallbackQuery $Query, TelegramUser $User, Telegram $Telegram): void
    {
        $Telegram->AnswerCallbackQuery($Query->ID);

        if($Query->Message != null && $Query->Message->Chat != null)
        {
            $Telegram->DeleteMessage($Query->Message->Chat->ID, $Query->Message->MessageID);
        }
    }

    public function OnInit(TelegramUser $User, Telegram $Telegram, array $Models = []): void {}
}