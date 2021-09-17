<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class Chat extends TelegramModel
{
    public function __construct(
        public int $ID,
        public string $Type,
        public ?string $Title,
        public ?string $UserName,
        public ?string $FirstName,
        public ?string $LastName,
        public ?Photo $Photo,
        public ?string $Bio,
        public ?string $Description,
        public ?string $InviteLink,
        public ?Message $PinnedMessage,
        public ?ChatPermissions $Permissions,
        public ?int $SlowModeDelay,
        public ?int $MessageAutoDeleteTime,
        public ?string $StickerSetName,
        public ?bool $CanSetStickerSet,
        public ?int $LinkedChatID,
        public ?ChatLocation $Location
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
                Type: $Object->{'type'},
                Title: $Object->{'title'} ?? null,
                UserName: $Object->{'username'} ?? null,
                FirstName: $Object->{'first_name'} ?? null,
                LastName: $Object->{'last_name'} ?? null,
                Photo: Photo::FromTelegramFormat($Object->{'photo'} ?? null),
                Bio: $Object->{'bio'} ?? null,
                Description: $Object->{'description'} ?? null,
                InviteLink: $Object->{'invite_link'} ?? null,
                PinnedMessage: Message::FromTelegramFormat($Object->{'pinned_message'} ?? null, $Object->{'type'} == 'channel'),
                Permissions: ChatPermissions::FromTelegramFormat($Object->{'permissions'} ?? null),
                SlowModeDelay: $Object->{'slow_mode_delay'} ?? null,
                MessageAutoDeleteTime: $Object->{'message_auto_delete_time'} ?? null,
                StickerSetName: $Object->{'sticker_set_name'} ?? null,
                CanSetStickerSet: $Object->{'can_set_sticker_set'} ?? false,
                LinkedChatID: $Object->{'linked_chat_id'} ?? null,
                Location: ChatLocation::FromTelegramFormat($Object->{'location'} ?? null)
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

