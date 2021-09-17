<?php

/*
	WeRtOG
	BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

class InputFile extends TelegramModel
{
    public ?string $Path;

	public function __construct(string $Path) {
        if(file_exists($Path))
            $this->Path = $Path;
    }

    public function GetBasename(): string
    {
        return basename($this->Path);
    }

    public function ToTelegramFormat(?array &$FilesOutput = null): mixed
    {
        return fopen($this->Path, 'r');
    }
}

