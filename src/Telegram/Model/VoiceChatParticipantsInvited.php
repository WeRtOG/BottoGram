<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class VoiceChatParticipantsInvited extends TelegramModel
{
    public function __construct(
        public UsersArray $Users
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Users: UsersArray::FromTelegramFormat($Object->{'users'})
            );
        }

        return null;
    }
}

