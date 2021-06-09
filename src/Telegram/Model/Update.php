<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

	class Update
	{
        public string $Type;

		public function __construct(
            public int $ID,
            public Request $Request,
            public ?Message $Message,
            public ?InlineQuery $InlineQuery,
            public ?PreCheckoutQuery $PreCheckoutQuery
		)
        {
            if($Message != null)
                $this->Type = UpdateType::Message;

            if($InlineQuery != null)
                $this->Type = UpdateType::InlineQuery;

            if($PreCheckoutQuery != null)
                $this->Type = UpdateType::PreCheckoutQuery;
        }
	}

?>