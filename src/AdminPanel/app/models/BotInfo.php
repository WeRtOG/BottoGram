<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel;

use WeRtOG\BottoGram\Telegram\Model\User;
use WeRtOG\BottoGram\Telegram\Model\WebhookInfo;

class BotInfo
{

    public function __construct(
        public bool $IsWebhook,
        public ?WebhookInfo $WebhookInfo,
        public ?User $Account
    )
    { }

}