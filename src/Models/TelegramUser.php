<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Models;

use WeRtOG\BottoGram\DatabaseManager\Models\ChangedProperty;
use WeRtOG\BottoGram\DatabaseManager\Models\DatabaseTableItemModel;

class TelegramUser extends DatabaseTableItemModel
{
    public int $ID = 1;
    public string $ChatID;
    public string $UserName;
    public string $FullName;
    public string $Nav;
    public ?array $Cache;
    public string $LastMediaGroup;
    public bool $HasNewMediaGroup = false;
    public string $RegistrationDate;

    private $NavigationChangeAction;

    public function __construct(array $Parameters = [])
    {
        if(isset($Parameters['Cache']) && !empty($Parameters['Cache']))
        {
            if(self::IsJSONString($Parameters['Cache']))
            {
                $Parameters['Cache'] = json_decode($Parameters['Cache'], true);
            }
        }

        parent::__construct($Parameters);
    }

    public function NavigateTo(string $MenuName): void
    {
        $this->Nav = $MenuName;

        if(is_callable($this->NavigationChangeAction))
        {
            call_user_func($this->NavigationChangeAction);
        }

        $this->TriggerOnPropertyChangeAction(new ChangedProperty('Nav', $MenuName));
    }

    public function ReloadMenu(): void
    {
        $this->NavigateTo($this->Nav);
    }

    public function OnNavigated(?callable $Action): void
    {
        $this->NavigationChangeAction = $Action;
    }

    public function SetCache(?array $Cache): void
    {
        $this->Cache = $Cache;
        $this->TriggerOnPropertyChangeAction(new ChangedProperty('Cache', $Cache));
    }

    public function SetCacheItem(string $Name, mixed $Value): void
    {
        $this->Cache = is_array($this->Cache) ? $this->Cache : [];
        $this->Cache[$Name] = $Value;
        $this->TriggerOnPropertyChangeAction(new ChangedProperty('Cache', $this->Cache));
    }

    public function GetCache(): ?array
    {
        return $this->Cache;
    }

    public function GetCacheItem(string $Name): mixed
    {
        return $this->Cache[$Name] ?? null;
    }

    public function SetNewMediaGroup(string $MediaGroup): void
    {
        $this->LastMediaGroup = $MediaGroup;
        $this->HasNewMediaGroup = true;
        $this->TriggerOnPropertyChangeAction(new ChangedProperty('LastMediaGroup', $this->LastMediaGroup));
    }

    public static function IsJSONString(?string $String): bool
    {
        json_decode($String);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}