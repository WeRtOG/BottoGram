<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;
use WeRtOG\BottoGram\Navigation\Command;

class ChatMember extends TelegramModel
{
    public function __construct(
        public User $User,
        public string $Status,
        public ?string $CustomTitle,
        public ?bool $IsAnonymous,
        public ?bool $CanBeEdited,
        public ?bool $CanManageChat,
        public ?bool $CanPostMessages,
        public ?bool $CanEditMessages,
        public ?bool $CanDeleteMessages,
        public ?bool $CanManageVoiceChats,
        public ?bool $CanRestrictMembers,
        public ?bool $CanPromoteMembers,
        public ?bool $CanChangeInfo,
        public ?bool $CanInviteUsers,
        public ?bool $CanPinMessages,
        public ?bool $IsMember,
        public ?bool $CanSendMessages,
        public ?bool $CanSendMediaMessages,
        public ?bool $CanSendPolls,
        public ?bool $CanSendOtherMessages,
        public ?bool $CanAddWebPagePreviews,
        public ?DateTime $UntilDate
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                User: User::FromTelegramFormat($Object->{'user'}),
                Status: $Object->{'status'},
                CustomTitle: $Object->{'custom_title'} ?? null,
                IsAnonymous: $Object->{'is_anonymous'} ?? null,
                CanBeEdited: $Object->{'can_be_edited'} ?? null,
                CanManageChat: $Object->{'can_manage_chat'} ?? null,
                CanPostMessages: $Object->{'can_post_messages'} ?? null,
                CanEditMessages: $Object->{'can_edit_messages'} ?? null,
                CanDeleteMessages: $Object->{'can_delete_messages'} ?? null,
                CanManageVoiceChats: $Object->{'can_manage_voice_chats'} ?? null,
                CanRestrictMembers: $Object->{'can_restrict_members'} ?? null,
                CanPromoteMembers: $Object->{'can_promote_members'} ?? null,
                CanChangeInfo: $Object->{'can_change_info'} ?? null,
                CanInviteUsers: $Object->{'can_invite_users'} ?? null,
                CanPinMessages: $Object->{'can_pin_messages'} ?? null,
                IsMember: $Object->{'is_member'} ?? null,
                CanSendMessages: $Object->{'can_send_messages'} ?? null,
                CanSendMediaMessages: $Object->{'can_send_media_messages'} ?? null,
                CanSendPolls: $Object->{'can_send_polls'} ?? null,
                CanSendOtherMessages: $Object->{'can_send_other_messages'} ?? null,
                CanAddWebPagePreviews: $Object->{'can_add_web_page_previews' ?? null},
                UntilDate: isset($Object->{'until_date'}) ? DateTime::createFromFormat('U', $Object->{'until_date'}) : null
            );
        }

        return null;
    }
}

