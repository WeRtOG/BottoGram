<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class WebhookInfo extends TelegramModel
{
	public function __construct(
		public string $Url,
        public bool $HasCustomCertificate,
        public int $PendingUpdateCount,
        public ?string $IPAddress,
        public ?DateTime $LastErrorDate,
        public ?string $LastErrorMessage,
        public ?int $MaxConnections,
        public ?array $AllowedUpdates
	) {}

	public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Url: $Object->{'url'},
                HasCustomCertificate: $Object->{'has_custom_certificate'} ?? false,
                PendingUpdateCount: $Object->{'pending_update_count'},
                IPAddress: $Object->{'ip_address'} ?? null,
                LastErrorDate: isset($Object->{'last_error_date'}) ? DateTime::createFromFormat('U', $Object->{'last_error_date'}) : null,
                LastErrorMessage: $Object->{'last_error_message'} ?? null,
                MaxConnections: $Object->{'max_connections'} ?? null,
                AllowedUpdates: $Object->{'allowed_updates'} ?? null
            );
        }

        return null;
    }
}

