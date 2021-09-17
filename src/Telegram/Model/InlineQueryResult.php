<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResult extends TelegramModel
{
	public function __construct(
        public string $Type,
        public string $ID
    ) { }
}

