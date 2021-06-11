<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

    class Audio extends Document
	{
        public function __construct(
            public string $FileID,
            public string $FileUniqueID,
            public int $Duration,
            public ?string $Performer,
            public ?string $Title,
            public ?string $FileName,
            public ?string $MimeType,
            public ?int $FileSize,
            public ?PhotoSize $Thumb,
        )
        { }

        public static function FromTelegramFormat(?object $Object): ?self
        {
            if($Object != null)
            {
                return new self(
                    FileID: $Object->{'file_id'},
                    FileUniqueID: $Object->{'file_unique_id'},
                    Duration: $Object->{'duration'},
                    Performer: $Object->{'performer'} ?? null,
                    Title: $Object->{'title'} ?? null,
                    FileName: $Object->{'file_name'} ?? null,
                    MimeType: $Object->{'mime_type'} ?? null,
                    FileSize: $Object->{'file_size'} ?? null,
                    Thumb: PhotoSize::FromTelegramFormat($Object->{'thumb'})
                );
            }

            return null;
        }
	}

?>