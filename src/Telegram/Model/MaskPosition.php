<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class MaskPosition extends TelegramModel
{
    public function __construct(
        public string $Point,
        public float $XShift,
        public float $YShift,
        public float $Scale
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                Point: $Object->{'point'},
                XShift: $Object->{'x_shift'},
                YShift: $Object->{'y_shift'},
                Scale: $Object->{'scale'}
            );
        }

        return null;
    }
}

