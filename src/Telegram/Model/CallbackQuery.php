<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class CallbackQuery extends TelegramModel
{
    public string $DataCommand = '';
	public array $DataArguments = [];

	public function __construct(
		public int $ID,
		public User $From,
		public string $ChatInstance,
        public ?Message $Message,
        public ?string $InlineMessageID,
        public ?string $Data,
        public ?string $GameShortName
	) {
        if($this->Data != null)
        {
            $TextParts = explode(' ', $this->Data);

            if(is_array($TextParts))
            {
                $this->DataCommand = $TextParts[0];

                array_shift($TextParts);
                $this->DataArguments = $TextParts;
            }
        }
    }

	public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
                From: User::FromTelegramFormat($Object->{'from'}),
                ChatInstance: $Object->{'chat_instance'},
                Message: Message::FromTelegramFormat($Object->{'message'} ?? null),
                InlineMessageID: $Object->{'inline_message_id'} ?? null,
                Data: $Object->{'data'} ?? null,
                GameShortName: $Object->{'game_short_name'} ?? null
            );
        }

        return null;
    }
}

