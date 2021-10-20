<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Models;

use WeRtOG\FoxyMVC\Model;

class RequestLog extends Model
{

    public int $ID;
    public int $UserID;
    public ?string $UserURL;
    public string $ChatID;
    public string $Date;
    public ?string $Request;
    public ?string $Response;
    public ?int $RequestCode;
    public ?int $ResponseCode;
    public ?string $RequestError;
    public ?string $ResponseError;

}