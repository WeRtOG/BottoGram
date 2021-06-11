<?php

	/*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\Telegram\Model;

    class PhotoSize
	{
        public function __construct(
            public string $FileID,
            public string $FileUniqueID,
            public int $Width,
            public int $Height,
            public ?int $FileSize
        )
        { }

        public static function FromTelegramFormat(?object $Object): ?self
        {
            if($Object != null)
            {
                return new self(
                    FileID: $Object->{'file_id'},
                    FileUniqueID: $Object->{'file_unique_id'},
                    Width: $Object->{'width'},
                    Height: $Object->{'height'},
                    FileSize: $Object->{'file_size'}
                );
            }

            return null;
        }
	}

?>