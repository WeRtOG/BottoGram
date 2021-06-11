<?php

/*
    WeRtOG
    BottoGram
*/
namespace WeRtOG\BottoGram\Telegram\Model;

use WeRtOG\FoxyMVC\ModelHelper;

/**
 * Класс запроса на подтверждение оплаты
 * @param int $Id ID
 * @param string $ChatID ID чата/пользователя
 */
class PreCheckoutQuery
{
    public function __construct(
        public int $ID,
        public string $ChatID
    ) {}
}

?>