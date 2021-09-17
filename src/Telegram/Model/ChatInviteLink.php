<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class ChatInviteLink extends TelegramModel
{
    public function __construct(
        public string $InviteLink,
        public User $Creator,
        public bool $IsPrimary,
        public bool $IsRevoked,
        public ?DateTime $ExpireDate,
        public ?int $MemberLimit
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                InviteLink: $Object->{'invite_link'},
                Creator: User::FromTelegramFormat($Object->{'creator'}),
                IsPrimary: $Object->{'is_primary'} ?? false,
                IsRevoked: $Object->{'is_remoked'} ?? false,
                ExpireDate: isset($Object->{'expire_date'}) ? DateTime::createFromFormat('U', $Object->{'expire_date'}) : null,
                MemberLimit: $Object->{'member_limit'} ?? null
            );
        }

        return null;
    }
}

