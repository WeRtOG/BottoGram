<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

	class Message
	{
		public string $Type = 'text';
		public string $Command = '';
		public array $Arguments = [];

		/**
         * Конструктор класса сообщения
         * @param $Data Массив данных
         */
		public function __construct(
			public string $Text = '',
			public string $ChatID = '',
			public string $FromID = '',
			public string $MessageID = "-1",
			public string $UserName = '',
			public bool $IsPhoto = false,
			public bool $IsVideo = false,
			public bool $IsDocument = false, 
			public bool $IsMediaGroup = false, 
			public bool $IsPay = false,
			public bool $IsCallbackQuery = false,
			public string $MediaGroupID = '',
			public string $PhotoID = '',
			public string $VideoID = '',
			public string $DocumentID = '',
			public string $CallbackQueryID = '',
			public bool $HasAttachments = false,
			public string $UserFullName = '',
			public bool $IsLocation = false,
			public $Location = '',
			public ?object $Pay = null,
			public bool $IsFromGroup = false,
			public bool $IsChannelPost = false,
			public ?object $Data = null
		)
		{
			if($this->IsPhoto) $this->Type = 'photo';
			if($this->IsVideo) $this->Type = 'video';
			if($this->IsDocument) $this->Type = 'document';
			if($this->IsLocation) $this->Type = 'location';

			$EXP = explode(' ', $this->Text);

			if(is_array($EXP))
			{
				$this->Command = $EXP[0];

				array_shift($EXP);
				$this->Arguments = $EXP;
			}
		}
	}

?>