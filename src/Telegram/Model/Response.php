<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

	class Response
	{
		public bool $ok = false;
		public int $code = 200;
		public string $error = "";
		public $result;
		public string $raw;

		public function __construct(string $json = '')
		{
			$this->raw = $json;
			$array = (array)json_decode($json);

			if(array_key_exists('ok', $array))
			{
				$this->ok = $array['ok'];

				if($this->ok)
				{
					$this->result = $array['result'];
				}
				else
				{
					if(array_key_exists('error_code', $array)) $this->code = $array['error_code'];
					if(array_key_exists('description', $array)) $this->error = $array['description'];
				}
			}
		}

		public function GetMessageID(): ?int
		{
			if($this->ok)
			{
				return $this->result->{'message_id'} ?? null;
			}
			else
			{
				return null;
			}
		}

		public function __toString()
		{
			return $this->raw;
		}
	}
    
?>