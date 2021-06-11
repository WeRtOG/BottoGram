<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

/**
 * Класс инлайнового запроса
 * @property int $Id ID запроса
 * @property string $ChatID ID чата/пользователя
 * @property string $query Запрос
 */
class InlineQuery
{
	public function __construct(
		public int $ID,
		public string $ChatID,
		public string $Query
	) {}
}

?>