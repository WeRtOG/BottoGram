<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\DatabaseManager\Models;

use WeRtOG\FoxyMVC\Model;

class DatabaseTableItemModel extends Model
{
    private $OnPropertyChangeAction;

    public function __construct(array $Parameters = [])
    {
        parent::__construct($Parameters);
    }

    public function OnPropertyChange(?callable $Action): void
    {
        $this->OnPropertyChangeAction = $Action;
    }

    public function TriggerOnPropertyChangeAction(ChangedProperty $Property): void
    {
        if(is_callable($this->OnPropertyChangeAction))
            call_user_func($this->OnPropertyChangeAction, $Property);
    }
}