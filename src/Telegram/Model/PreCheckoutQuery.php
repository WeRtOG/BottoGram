<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class PreCheckoutQuery extends TelegramModel
{
    public function __construct(
        public int $ID,
        public User $From,
        public string $Currency,
        public int $TotalAmount,
        public string $InvoicePayload,
        public ?string $ShippingOptionID,
        public ?OrderInfo $OrderInfo
    ) {}

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
				From: User::FromTelegramFormat($Object->{'from'}),
				Currency: $Object->{'currency'},
                TotalAmount: $Object->{'total_amount'},
                InvoicePayload: $Object->{'invoice_payload'},
                ShippingOptionID: $Object->{'shipping_option_id'} ?? null,
                OrderInfo: OrderInfo::FromTelegramFormat($Object->{'order_info'} ?? null)
            );
        }

        return null;
    }
}

