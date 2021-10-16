<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram;

$AutoloadFile = __DIR__ . '/../vendor/autoload.php';
if(file_exists($AutoloadFile)) require_once $AutoloadFile;

use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseConnection;
use WeRtOG\BottoGram\Exceptions\BottoConfigException;
use WeRtOG\FoxyMVC\Exceptions\ModelException;
use WeRtOG\FoxyMVC\ModelHelper;

class BottoConfig
{
    public string $Name;
    public string $Token;
    public DatabaseConnection $DatabaseConnection;
    public string $SessionUser = 'botto_user';
    public string $Logo = '';
    public bool $Private = false;
    public array $PrivateAllow = [];
    public string $AdminContact = "@WeRtOG";
    public bool $ButtonsAutoSize = true;
    public bool $AllowGroups = true;
    public bool $EnableTextLog = true;
    public bool $EnableExtendedLog = false;
    public string $ConfigFile = '';

    public function __construct(array $Data)
    {
        try
        {
            ModelHelper::SetParametersFromArray($this, $Data, true, true);
        }
        catch(ModelException $Exception)
        {
            throw new BottoConfigException($Exception->getMessage());
        }
    }

    public static function ParseJSON(string $JSON): array
    {
        return json_decode($JSON, true);
    }


    public static function ParseJSONFile(string $Filename): array
    {
        $Config = self::ParseJSON(
            file_get_contents($Filename)
        );
        $Config['ConfigFile'] = $Filename;

        return $Config;
    }

    public static function CreateFromJSONFile(string $Filename): self
    {
        $ParsedData = self::ParseJSONFile($Filename);
        
        try
        {
            if(isset($ParsedData['DatabaseConnection']))
                $ParsedData['DatabaseConnection'] = new DatabaseConnection($ParsedData['DatabaseConnection']);
        }
        catch(ModelException $Exception)
        {
            throw new BottoConfigException($Exception->getMessage());
        }

        return new self($ParsedData);
    }

    public static function ChangeParameter(string $Name, $Value, string $ConfigFile): bool
    {
        if(file_exists($ConfigFile))
        {
            $Config = self::ParseJSONFile($ConfigFile);
            $Config[$Name] = $Value;
            unset($Config['ConfigFile']);

            file_put_contents($ConfigFile, json_encode($Config, JSON_PRETTY_PRINT));

            return true;
        }
        else
        {
            return false;
        }
    }

    public static function ChangeToken(string $Token, string $ConfigFile): int
    {
        return self::ChangeParameter('Token', $Token, $ConfigFile);
    }
    
}