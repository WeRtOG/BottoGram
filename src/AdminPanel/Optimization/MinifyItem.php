<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\AdminPanel\Optimization;


class MinifyItem
{

    public function __construct(
        public array $Source,
        public string $Result
    ) { }

}