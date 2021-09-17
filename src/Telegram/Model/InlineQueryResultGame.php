<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultGame extends TelegramModel
{
    public string $Type = MediaType::Game;

	public function __construct(
        public string $ID,
        public string $GameShortName,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
    )
    { }
}

