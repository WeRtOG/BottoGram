<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputInvoiceMessageContent extends InputMessageContent
{
    public function __construct(
        public string $Title,
        public string $Description,
        public string $Payload,
        public string $ProviderToken,
        public string $Currency,
        public LabeledPriceArray $Prices,
        public ?int $MaxTipAmount = null,
        public ?array $SuggestedTipAmounts = null,
        public ?string $ProviderData = null,
        public ?string $PhotoUrl = null,
        public ?int $PhotoSize = null,
        public ?int $PhotoWidth = null,
        public ?int $PhotoHeight = null,
        public ?bool $NeedName = null,
        public ?bool $NeedPhoneNumber = null,
        public ?bool $NeedEmail = null,
        public ?bool $NeedShippingAddress = null,
        public ?bool $SendPhoneNumberToProvider = null,
        public ?bool $SendEmailToProvider = null,
        public ?bool $IsFlexible = null
    )
    { }
}

