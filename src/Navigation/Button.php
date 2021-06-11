<?php
/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Navigation;

use WeRtOG\FoxyMVC\ModelHelper;

class Button
{
    public function __construct(
        public string $Title,
        public mixed $Action = null,
        public bool $RequestContact = false,
        public bool $RequestLocation = false,
    )
    { }
}
?>