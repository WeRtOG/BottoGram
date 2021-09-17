<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram;

use WeRtOG\BottoGram\DatabaseManager\Database;
use WeRtOG\BottoGram\Telegram\Model\Request;
use WeRtOG\BottoGram\Telegram\Model\Response;
use WeRtOG\BottoGram\Telegram\Model\ResponseData;

/**
 * Модуль для работы с логами
 * @property Database $Database База данных
 * @property string|null $ChatID ID пользователя (Telegram)
 * @property int $Row ID строки
 * @property bool $EnableTextLog Флаг текстовых логов
 */
class Log
{
    public Database $Database;
    public ?string $ChatID;
    public int $Row;
    public bool $EnableTextLog;
    public bool $EnableExtendedLog;

    private string $Table;


    public function __construct(?string $ChatID, Request $Request, Database $Database, bool $EnableTextLog = true, bool $EnableExtendedLog = false)
    {
        $this->Database = $Database;
        $this->ChatID = $ChatID;
        $this->Table = BOTTOGRAM_DB_TABLE_BOTLOG;

        $Request = $Database->EscapeString($Request);

        $this->EnableTextLog = $EnableTextLog;
        $this->EnableExtendedLog = $EnableExtendedLog;

        if($this->EnableExtendedLog)
            $this->Database->FetchQuery("INSERT INTO $this->Table (ChatID, Request) VALUES ('$ChatID', '$Request')");
        else
            $this->Database->FetchQuery("INSERT INTO $this->Table (ChatID) VALUES ('$ChatID')");

        $this->Row = $this->Database->GetInsertID();

        $this->WriteToLogs('');
        $this->WriteToLogs('👾 App started.');
        $this->WriteToLogs('_______________________________________');
        
        if($ChatID != -1)
        {
            $this->WriteToLogs('👥 ChatID: ' . $ChatID);
        }
        
    }


    public function WriteToLogs(string $String)
    {
        if($this->EnableTextLog)
        {
            file_put_contents('BottoGram.log', date('Y-m-d H:i:s') . ': ' . $String . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    public function RequestSuccess()
    {
        $Row = $this->Row;
        $this->Database->FetchQuery("UPDATE $this->Table SET RequestCode='200' WHERE id='$Row'");

        $this->WriteToLogs('');
        $this->WriteToLogs('✅⬅️ Request: ok');
    }

    public function RequestFail(?int $Code, string $Error)
    {
        $Row = $this->Row;
        $Error = $this->Database->EscapeString($Error);
        $this->Database->FetchQuery("UPDATE $this->Table SET RequestCode='$Code', RequestError='$Error' WHERE id='$Row'");

        $this->WriteToLogs('');
        $this->WriteToLogs('❌⬅️ Request: fail');
        $this->WriteToLogs('Code: ' . $Code);
        $this->WriteToLogs('Error: ' . $Error);
    }

    public function ResponseSuccess(ResponseData $Response)
    {
        $Response = $this->Database->EscapeString($Response);
        $Row = $this->Row;

        if($this->EnableExtendedLog)
            $this->Database->FetchQuery("UPDATE $this->Table SET ResponseCode='200', Response='$Response' WHERE ID='$Row'");
        else
            $this->Database->FetchQuery("UPDATE $this->Table SET ResponseCode='200' WHERE ID='$Row'");

        $this->WriteToLogs('');
        $this->WriteToLogs('✅➡️ Response: ok');
    }

    public function ResponseFail(?int $Code, ?string $Error, ResponseData $Response)
    {
        $Response = $this->Database->EscapeString($Response);
        $Row = $this->Row;
        $Error = $this->Database->EscapeString($Error);

        if($this->EnableExtendedLog)
            $this->Database->FetchQuery("UPDATE $this->Table SET ResponseCode='$Code', ResponseError='$Error', Response='$Response' WHERE id='$Row'");
        else
            $this->Database->FetchQuery("UPDATE $this->Table SET ResponseCode='$Code', ResponseError='$Error' WHERE id='$Row'");

        $this->WriteToLogs('');
        $this->WriteToLogs('❌➡️ Response: fail');
        $this->WriteToLogs('Сode: ' . $Code);
        $this->WriteToLogs('Error: ' . $Error);
    }

    public function ProcessResponse(Response $Response): void
    {
        $ResponseData = $Response->GetData();
        if($ResponseData != null)
        {
            if($ResponseData->ok)
            {
                $this->ResponseSuccess($ResponseData);
            }
            else
            {
                $this->ResponseFail($ResponseData->code, $ResponseData->error, $ResponseData);
            }
        }
    }
}