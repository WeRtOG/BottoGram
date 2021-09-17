<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InlineQueryResultContact extends InlineQueryResult
{
    public string $Type = MediaType::Contact;

	public function __construct(
        public string $ID,
        public string $PhoneNumber,
        public string $FirstName,
        public ?string $LastName = null,
        public ?string $VCard = null,
        public ?InlineKeyboardMarkup $ReplyMarkup = null,
        public ?InputMessageContent $InputMessageContent = null,
        public ?string $ThumbUrl = null,
        public ?int $ThumbWidth = null,
        public ?int $ThumbHeight = null
    )
    { }
}

