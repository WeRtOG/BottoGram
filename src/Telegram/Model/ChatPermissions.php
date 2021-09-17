<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class ChatPermissions extends TelegramModel
{
    public function __construct(
        public bool $CanSendMessages,
        public bool $CanSendMediaMessages,
        public bool $CanSendPolls,
        public bool $CanSendOtherMessages,
        public bool $CanAddWebPagePreviews,
        public bool $CanChangeInfo,
        public bool $CanInviteUsers,
        public bool $CanPinMessages
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                CanSendMessages: $Object->{'can_send_messages'} ?? false,
                CanSendMediaMessages: $Object->{'can_send_media_messages'} ?? false,
                CanSendPolls: $Object->{'can_send_polls'} ?? false,
                CanSendOtherMessages: $Object->{'can_send_other_messages'} ?? false,
                CanAddWebPagePreviews: $Object->{'can_add_web_page_previews'} ?? false,
                CanChangeInfo: $Object->{'can_change_info'} ?? false,
                CanInviteUsers: $Object->{'can_invite_users'} ?? false,
                CanPinMessages: $Object->{'can_pin_messages'} ?? false
            );
        }

        return null;
    }
}

