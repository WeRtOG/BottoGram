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

		public bool $HasAttachments = false;

		/**
         * Конструктор класса сообщения
         * @param $Data Массив данных
         */
		public function __construct(
			public string $Text,
			public ?string $ChatID,
			public ?string $FromID,
			public string $MessageID = "-1",
			public string $UserName = '',
			public bool $IsCallbackQuery = false,
			public ?string $MediaGroupID = null,

			public ?Photo $Photo = null,
			public ?Video $Video = null,
			public ?Document $Document = null,
			public ?Audio $Audio = null,

			public ?string $PhotoID = null,
			public ?string $VideoID = null,
			public ?string $DocumentID = null,

			public ?string $CallbackQueryID = null,
			public string $UserFullName = '',
			public ?object $Pay = null,
			public bool $IsFromGroup = false,
			public bool $IsChannelPost = false,
			public ?object $Data = null,
			public ?Location $Location = null
		)
		{
			if($this->Photo != null) $this->Type = MessageType::Photo;
			if($this->Video != null) $this->Type = MessageType::Video;
			if($this->Document != null) $this->Type = MessageType::Document;
			if($this->Audio != null) $this->Type = MessageType::Audio;
			if($this->Location != null) $this->Type = MessageType::Location;
			if($this->MediaGroupID != null) $this->Type = MessageType::MediaGroup;

			$this->HasAttachments = (isset($this->Photo) || isset($this->Video) || isset($this->Document));

			$EXP = explode(' ', $this->Text);

			if(is_array($EXP))
			{
				$this->Command = $EXP[0];

				array_shift($EXP);
				$this->Arguments = $EXP;
			}

			print_r($this);
		}
	}

?>