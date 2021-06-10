<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

	class Message
	{
		public string $Type = MessageType::Text;
		public string $Command = MessageType::Text;
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
			public bool $IsCallbackQuery = false,
			public ?string $MediaGroupID = null,
			public ?string $PhotoID = null,
			public ?string $VideoID = null,
			public ?string $DocumentID = null,
			public string $CallbackQueryID = '',
			public bool $HasAttachments = false,
			public string $UserFullName = '',
			public ?object $Pay = null,
			public bool $IsFromGroup = false,
			public bool $IsChannelPost = false,
			public ?object $Data = null,
			public ?array $Location = null
		)
		{
			if($this->PhotoID != null) $this->Type = MessageType::Photo;
			if($this->VideoID != null) $this->Type = MessageType::Video;
			if($this->DocumentID != null) $this->Type = MessageType::Document;
			if($this->Location != null) $this->Type = MessageType::Location;
			if($this->MediaGroupID != null) $this->Type = MessageType::MediaGroup;

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