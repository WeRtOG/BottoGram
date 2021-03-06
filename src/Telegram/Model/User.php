<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class User extends TelegramModel
{
    public function __construct(
        public int $ID,
        public bool $IsBot,
        public string $FirstName,
        public ?string $LastName,
        public ?string $UserName,
        public ?string $LanguageCode,
        public ?bool $CanJoinGroups,
        public ?bool $CanReadAllGroupMessages,
        public ?bool $SupportsInlineQueries
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
                IsBot: $Object->{'is_bot'},
                FirstName: $Object->{'first_name'},
                LastName: $Object->{'last_name'} ?? null,
                UserName: $Object->{'username'} ?? null,
                LanguageCode: $Object->{'language_code'} ?? null,
                CanJoinGroups: $Object->{'can_join_groups'} ?? null,
                CanReadAllGroupMessages: $Object->{'can_read_all_group_messages'} ?? null,
                SupportsInlineQueries: $Object->{'supports_inline_queries'} ?? null
            );
        }

        return null;
    }

    public function GetUsername(): string
    {
        return isset($this->UserName) ? $this->UserName : $this->ID;
    }

    public function GetFullName(): string
    {
        return implode(' ', [$this->FirstName ?? null, $this->LastName ?? null]);
    }
}

