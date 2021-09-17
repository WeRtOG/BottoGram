<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class ProximityAlertTriggered extends TelegramModel
{
    public function __construct(
        public User $Traveler,
        public User $Watcher,
        public int $Distance
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Traveler: User::FromTelegramFormat($Object->{'traveler'}),
                Watcher: User::FromTelegramFormat($Object->{'watcher'}),
                Distance: $Object->{'distance'}
            );
        }

        return null;
    }
}

