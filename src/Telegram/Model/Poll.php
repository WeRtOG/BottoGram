<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use DateTime;

class Poll
{
    public function __construct(
        public string $ID,
        public string $Question,
        public PollOptions $Options,
        public int $TotalVoterCount,
        public bool $IsClosed,
        public bool $IsAnonymous,
        public string $Type,
        public bool $AllowsMultipleAnswers,
        public ?int $CorrectOptionID,
        public ?string $Explanation,
        public ?MessageEntities $ExplanationEntities,
        public ?int $OpenPeriod,
        public ?DateTime $CloseDate
    )
    { }

    public static function FromTelegramFormat(?object $Object): ?self
    {
        if($Object != null)
        {
            return new self(
                ID: $Object->{'id'},
                Question: $Object->{'question'},
                Options: PollOptions::FromTelegramFormat($Object->{'options'}),
                TotalVoterCount: $Object->{'total_voter_count'},
                IsClosed: $Object->{'is_closed'},
                IsAnonymous: $Object->{'is_anonymous'},
                Type: $Object->{'type'},
                AllowsMultipleAnswers: $Object->{'allows_multiple_answers'},
                CorrectOptionID: $Object->{'correct_option_id'} ?? null,
                Explanation: $Object->{'explanation'} ?? null,
                ExplanationEntities: MessageEntities::FromTelegramFormat($Object->{'explanation_entities'} ?? null),
                OpenPeriod: $Object->{'open_period'} ?? null,
                CloseDate: isset($Object->{'close_date'}) ? DateTime::createFromFormat('U', $Object->{'close_date'}) : null
            );
        }

        return null;
    }
}

?>