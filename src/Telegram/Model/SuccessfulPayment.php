<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class SuccessfulPayment
{
    public function __construct(
        public string $Currency,
        public int $TotalAmount,
        public string $InvoicePayload,
        public string $TelegramPaymentChargeID,
        public string $ProviderPaymentChargeID,
        public ?string $ShippingOptionID,
        public ?OrderInfo $OrderInfo
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Currency: $Object->{'currency'},
                TotalAmount: $Object->{'total_amount'},
                InvoicePayload: $Object->{'invoice_payload'},
                TelegramPaymentChargeID: $Object->{'telegram_payment_charge_id'},
                ProviderPaymentChargeID: $Object->{'provider_payment_charge_id'},
                ShippingOptionID: $Object->{'shipping_option_id'} ?? null,
                OrderInfo: OrderInfo::FromTelegramFormat($Object->{'order_info'} ?? null)
            );
        }

        return null;
    }
}

?>