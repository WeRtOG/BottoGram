<?php

    /*
        WeRtOG
        BottoGram
    */
    namespace WeRtOG\BottoGram\AdminPanel;

    use WeRtOG\FoxyMVC\Model;

    class AdminUser extends Model
    {

        public int $ID;
        public string $Login;
        public string $Password;

    }

?>