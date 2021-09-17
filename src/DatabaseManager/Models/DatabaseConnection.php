<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\DatabaseManager\Models;

use WeRtOG\FoxyMVC\ModelHelper;

class DatabaseConnection
{
    public string $Server;
    public string $User;
    public string $Password;
    public string $Database;

    public function __construct(array $Parameters = [])
    {
        ModelHelper::SetParametersFromArray($this, $Parameters, false, true);
    }
}