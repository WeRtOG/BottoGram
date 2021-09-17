<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQuery extends TelegramModel
{
	public function __construct(
		public int $ID,
		public User $From,
		public string $Query,
		public string $Offset,
		public ?string $ChatType,
		public ?Location $Location
	) {}

	public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
				From: User::FromTelegramFormat($Object->{'from'}),
				Query: $Object->{'query'},
				Offset: $Object->{'offset'},
				ChatType: $Object->{'chat_type'} ?? null,
				Location: Location::FromTelegramFormat($Object->{'location'} ?? null)
            );
        }

        return null;
    }
}

