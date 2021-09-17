<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;


class Request extends TelegramModel
{
	public object $Body;
	public string $Raw;

	/**
	 * Конструктор класса ответа API Telegram
	 * @param $json JSON
	 */
	public function __construct(string $Input = '')
	{
		$this->Body = json_decode($Input);
		$this->Raw = $Input;
	}

	public function __toString()
	{
		return $this->Raw;
	}
}

