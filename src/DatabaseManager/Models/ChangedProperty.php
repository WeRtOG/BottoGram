<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\DatabaseManager\Models;

class ChangedProperty
{
    public function __construct(
        public string $Name,
        public mixed $Value
    )
    { }
}