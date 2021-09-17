<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use GuzzleHttp\Promise\Promise;

class Response
{
	private ?Promise $Promise;
	private ?ResponseData $Data = null;

	public function __construct(?Promise $Promise = null)
	{
		$this->Promise = $Promise;
	}

	public function GetData(): ResponseData
	{
		if($this->Data == null && $this->Promise != null)
		{
			$HttpResponse = $this->Promise->wait();
			$this->Data = new ResponseData((string)$HttpResponse->getBody());
		}

		return $this->Data ?? new ResponseData();
	}
}

