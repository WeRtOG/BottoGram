<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use ArrayIterator;

class PassportFiles extends ArrayIterator
{
    public function __construct(PassportFile ...$PassportFiles)
    {
        parent::__construct($PassportFiles);
    }

    public function current(): PassportFile
    {
        return parent::current();
    }

    public function offsetGet($Offset): PassportFile
    {
        return parent::offsetGet($Offset);
    }

    public function GetMaxSize(): PassportFile
    {
        return end(parent::getArrayCopy());
    }

    public function __toArray(): array
    {
        return parent::getArrayCopy();
    }

    public static function FromTelegramFormat(?array $PassportFilesInTelegramFormat): ?self
    {
        $PassportFiles = [];
        if($PassportFilesInTelegramFormat != null)
        {
            foreach($PassportFilesInTelegramFormat as $PassportFile)
            {
                $PassportFiles[] = PassportFile::FromTelegramFormat($PassportFile);
            }

            return new self(...$PassportFiles);
        }

        return null;
    }
}

?>